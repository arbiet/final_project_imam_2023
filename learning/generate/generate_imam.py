import datetime
import mysql.connector
import csv

# Connect to the MySQL database
db = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="skripsi-imam"
)
cursor = db.cursor()

# Open and read the CSV file
with open('data.csv', 'r') as csv_file:
    csv_reader = csv.reader(csv_file, delimiter=';')
    next(csv_reader)  # Skip the header row

    for row in csv_reader:
        # Assuming the columns in the CSV match the order in your INSERT statement
        no = row[0]
        nama = row[1]
        nipd = row[2]
        jenis_kelamin = row[3]
        nisn = row[4]
        tempat_lahir = row[5]
        tanggal_lahir = row[6]
        nik = row[7]
        agama = row[8]
        rombel = row[9]
        # tahun_masuk = rombel dipisah 2 digit awal adalah tingkat kelas, jika tingkat kelas 10 maka tahun masuk adalah tahun sekarang
        tahun_masuk = row[9][0:2]
        if tahun_masuk == 'X ' or tahun_masuk == 'X-' or tahun_masuk == 'X':
            tahun_masuk = 10
            if rombel[2] == 'A':
                rombel = '10 A'
        tahun_masuk = int(tahun_masuk)
        
        # datetime.datetime.now().year
        date = datetime.datetime.now()

        if tahun_masuk == 10:
            tahun_masuk = date.year
        elif tahun_masuk == 11:
            tahun_masuk = date.year - 1
        elif tahun_masuk == 12:
            tahun_masuk = date.year - 2

        # Define your INSERT query
        insert_query = "INSERT INTO `data_siswa` (`no`, `nama`, `nipd`, `jenis_kelamin`, `nisn`, `tempat_lahir`, `tanggal_lahir`, `nik`, `agama`, `rombel`, `tahun_masuk`) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)"

        # Execute the query with the provided data
        cursor.execute(insert_query, (no, nama, nipd, jenis_kelamin, nisn, tempat_lahir, tanggal_lahir, nik, agama, rombel, tahun_masuk))

        db.commit()

# Close the database connection
db.close()
