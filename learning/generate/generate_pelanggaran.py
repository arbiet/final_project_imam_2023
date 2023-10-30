import datetime
import random
import mysql.connector
from faker import Faker

# Connect to the MySQL database
db = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="skripsi-imam"
)
cursor = db.cursor()

# Create a Faker instance
fake = Faker()

# Get the list of pelanggaran and prestasi IDs from your previous SQL query
pelanggaran_ids = [4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33]
prestasi_ids = [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]

# Get the list of siswa IDs (1181 students) from get table data
siswa_ids = list(range(3544, 4724))

# Generate data_pelanggaran
data_pelanggaran = []
for siswa_id in siswa_ids:
    num_pelanggaran = random.randint(0, 10)
    pelanggaran_sample = random.sample(pelanggaran_ids, num_pelanggaran)
    total_pelanggaran_poin = 0

    
    for pelanggaran_id in pelanggaran_sample:
        pelanggaran_poin = cursor.execute(f"SELECT poin FROM master_pelanggaran WHERE id = {pelanggaran_id}")
        # change cursor to int
        pelanggaran_poin = cursor.fetchone()[0]
        total_pelanggaran_poin += pelanggaran_poin

        if total_pelanggaran_poin > 250:
            break

        today = datetime.datetime.now()
        start_date = today - datetime.timedelta(days=365)  # A year ago
        end_date = today
        tanggal = fake.date_time_between(start_date=start_date, end_date=end_date)
        jam = fake.date_time_between(start_date=tanggal, end_date=tanggal.replace(hour=16))
        created_at = datetime.datetime.now()
        updated_at = created_at
        
        data_pelanggaran.append((
            siswa_id,
            pelanggaran_id,
            tanggal,
            jam,
            created_at,
            updated_at
        ))

# Generate data_prestasi
data_prestasi = []
for siswa_id in siswa_ids:
    num_prestasi = random.randint(0, 5)
    prestasi_sample = random.sample(prestasi_ids, num_prestasi)
    
    for prestasi_id in prestasi_sample:
        today = datetime.datetime.now()
        start_date = today - datetime.timedelta(days=365)  # A year ago
        end_date = today
        tanggal = fake.date_time_between(start_date=start_date, end_date=end_date)
        jam = fake.date_time_between(start_date=tanggal, end_date=tanggal.replace(hour=16))
        nama_prestasi = fake.sentence(nb_words=3)
        penyelengara = fake.company()
        juara = fake.random_element(elements=('Juara 1', 'Juara 2', 'Juara 3'))
        detail = fake.paragraph()
        created_at = datetime.datetime.now()
        updated_at = created_at
        
        data_prestasi.append((
            siswa_id,
            prestasi_id,
            tanggal,
            jam,
            nama_prestasi,
            penyelengara,
            juara,
            detail,
            created_at,
            updated_at
        ))

# Insert generated data into the database
insert_pelanggaran_query = "INSERT INTO data_pelanggaran (siswa_id, pelanggaran_id, tanggal, jam, created_at, updated_at) VALUES (%s, %s, %s, %s, %s, %s)"
cursor.executemany(insert_pelanggaran_query, data_pelanggaran)

insert_prestasi_query = "INSERT INTO data_prestasi (siswa_id, prestasi_id, tanggal, jam, nama_prestasi, penyelengara, juara, detail, created_at, updated_at) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)"
cursor.executemany(insert_prestasi_query, data_prestasi)

# Commit changes and close the database connection
db.commit()

print('Done generating data_pelanggaran and data_prestasi')
print('Total data_pelanggaran:', len(data_pelanggaran))
print('Total data_prestasi:', len(data_prestasi))

db.close()
