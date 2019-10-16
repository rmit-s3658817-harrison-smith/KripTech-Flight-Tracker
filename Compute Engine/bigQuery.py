import requests 
import json 
from google.cloud import bigquery
from google.cloud import storage

def upload_blob(bucket_name, source_file_name, destination_blob_name):
    """Uploads a file to the bucket."""
    storage_client = storage.Client()
    bucket = storage_client.get_bucket(bucket_name)
    blob = bucket.blob(destination_blob_name)

    blob.upload_from_filename(source_file_name)

    print('File {} uploaded to {}.'.format(
        source_file_name,
        destination_blob_name))


client = bigquery.Client()
response = requests.get("https://opensky-network.org/api/states/all")

data = response.json()
printData = response.json()
data = data['states']

fileName = str(printData['time'])
data = printData['states']

print("Save data")
f = open("temp.txt", "w")

for row in data:
    contstructCSV = ""
    for dataVal in row:
        if isinstance(dataVal, int) or isinstance(dataVal, float) or dataVal is None:
            contstructCSV += str(dataVal) + ","
        else:
            contstructCSV += str(dataVal.encode('ascii', 'ignore').decode('ascii')).rstrip() + ","

    f.write(contstructCSV[:-1].replace("None","null")+"\n")


f.close()

upload_blob("flight_info","temp.txt",fileName+".txt")

print("Execute")
for row in data:
    icao24 = str(row[0])
    callsign = str(row[1].strip())
    origin_country = str(row[2])
    time_position = str(row[3])
    last_contact = str(row[4])
    longitude = str(row[5])
    latitude = str(row[6])
    baro_altitude= str(row[7])
    on_ground= str(row[8])
    velocity= str(row[9])
    true_track= str(row[10])
    vertical_rate= str(row[11])
    sensors = str(row[12])
    geo_altitude = str(row[13])
    squawk = str(row[14])
    spi = str(row[15])
    position_source = str(row[16])
 
    if icao24 == "" or callsign == "":
        continue
     
    nullString = 'INSERT INTO flight_info_all.flight_info VALUES ("'+icao24+'","'+callsign+'","'+origin_country+'",'+time_position+','+last_contact+','+longitude+','+latitude+','+ baro_altitude +','+ on_ground +','+ velocity +','+ true_track +',"'+ vertical_rate +'","'+ sensors +'","'+ geo_altitude +'","'+ squawk +'",'+ spi +','+ position_source +');'
    
    query = (
            nullString.replace("None","null")
            )
    query_job = client.query(
        query,
        # Location must match that of the dataset(s) referenced in the query.
        location="australia-southeast1",
    )
    for row in query_job:  # API request - fetches results
    # Row values can be accessed by field name or ind]
        print(row)

