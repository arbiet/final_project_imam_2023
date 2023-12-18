import datetime
import random
import mysql.connector
from faker import Faker

# Connect to the MySQL database
db = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="final_project_imam_2023"
)
cursor = db.cursor()

# Create a Faker instance
fake = Faker()

# Get the list of pelanggaran and prestasi IDs from your previous SQL query
pelanggaran_ids = list(range(1, 29))  # Update the range based on your actual data (ada 29 data)
prestasi_ids = list(range(1, 11))     # Update the range based on your actual data (ada 11 data)

# Fetch the list of siswa IDs from the Students table
cursor.execute("SELECT StudentID FROM Students")
siswa_ids = [row[0] for row in cursor.fetchall()]

# Generate data for StudentViolations
data_pelanggaran = []
for siswa_id in siswa_ids:
    num_pelanggaran = random.randint(0, 10)
    pelanggaran_sample = random.sample(pelanggaran_ids, num_pelanggaran)
    total_pelanggaran_poin = 0

    for pelanggaran_id in pelanggaran_sample:
        cursor.execute(f"SELECT Points FROM MasterViolations WHERE ViolationID = {pelanggaran_id}")
        result = cursor.fetchone()

        if result is not None:
            pelanggaran_poin = result[0]
            total_pelanggaran_poin += pelanggaran_poin

            if total_pelanggaran_poin > 150:
                break

            today = datetime.datetime.now()
            start_date = today - datetime.timedelta(days=365*2+182)  # A year ago
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

# Generate data for StudentAchievements
data_prestasi = []
for siswa_id in siswa_ids:
    num_prestasi = random.randint(0, 1)
    prestasi_sample = random.sample(prestasi_ids, num_prestasi)

    for prestasi_id in prestasi_sample:
        cursor.execute(f"SELECT Points FROM MasterAchievements WHERE AchievementID = {prestasi_id}")
        achievement_points = cursor.fetchone()[0]

        today = datetime.datetime.now()
        start_date = today - datetime.timedelta(days=365*2+182)  # A year ago
        end_date = today
        tanggal = fake.date_time_between(start_date=start_date, end_date=end_date)
        jam = fake.date_time_between(start_date=tanggal, end_date=tanggal.replace(hour=16))
        nama_prestasi = fake.sentence(nb_words=3)
        penyelenggara = fake.company()
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
            penyelenggara,
            juara,
            detail,
            created_at,
            updated_at
        ))

# Insert generated data into the database for StudentViolations
insert_pelanggaran_query = "INSERT INTO StudentViolations (StudentID, ViolationID, Date, Time, CreatedAt, UpdatedAt) VALUES (%s, %s, %s, %s, %s, %s)"
cursor.executemany(insert_pelanggaran_query, data_pelanggaran)

# Insert generated data into the database for StudentAchievements
insert_prestasi_query = "INSERT INTO StudentAchievements (StudentID, AchievementID, Date, Time, AchievementName, Organizer, Rank, Details, CreatedAt, UpdatedAt) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)"
cursor.executemany(insert_prestasi_query, data_prestasi)

# Commit changes and close the database connection
db.commit()

print('Done generating data_pelanggaran and data_prestasi')
print('Total data_pelanggaran:', len(data_pelanggaran))
print('Total data_prestasi:', len(data_prestasi))

db.close()
