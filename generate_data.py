import csv
import mysql.connector
from mysql.connector import Error

# Fungsi untuk membuat koneksi ke database
def create_connection():
    try:
        connection = mysql.connector.connect(
            host="localhost",
            user="root",
            password="",
            database="final_project_imam_2023"
        )
        if connection.is_connected():
            print(f"Connected to MySQL database (version {connection.get_server_info()})")
            return connection
    except Error as e:
        print(f"Error: {e}")
    return None

# Fungsi untuk memasukkan data ke tabel Users
def insert_into_users(
        connection, 
        username, 
        password, 
        email, 
        full_name, 
        date_of_birth, 
        gender,
        address, 
        phone_number,
        role_id, account_creation_date, last_login, account_status, profile_picture_url, activation_status):
    try:
        cursor = connection.cursor()
        query = f"INSERT INTO Users (Username, Password, Email, FullName, DateOfBirth, Gender, Address, PhoneNumber, RoleID, AccountCreationDate, LastLogin, AccountStatus, ProfilePictureURL, ActivationStatus) VALUES ('{username}', '{password}', '{email}', '{full_name}', '{date_of_birth}', '{gender}', '{address}', '{phone_number}', '{role_id}', '{account_creation_date}', '{last_login}', '{account_status}', '{profile_picture_url}', '{activation_status}')"
        cursor.execute(query)
        connection.commit()
        print(f"Data inserted into Users table: {full_name}")
    except Error as e:
        print(f"Error: {e}")

# Fungsi untuk memasukkan data ke tabel Students
def insert_into_students(connection, student_number, religion, parent_guardian_full_name, parent_guardian_address, parent_guardian_phone_number, parent_guardian_email, class_id, user_id):
    try:
        cursor = connection.cursor()
        query = f"INSERT INTO Students (StudentNumber, Religion, ParentGuardianFullName, ParentGuardianAddress, ParentGuardianPhoneNumber, ParentGuardianEmail, ClassID, UserID) VALUES ('{student_number}', '{religion}', '{parent_guardian_full_name}', '{parent_guardian_address}', '{parent_guardian_phone_number}', '{parent_guardian_email}', {class_id}, {user_id})"
        cursor.execute(query)
        connection.commit()
        print(f"Data inserted into Students table: {parent_guardian_full_name}")
    except Error as e:
        print(f"Error: {e}")

# Add a function to get ClassID based on 'rombel'
def get_class_id(connection, rombel):
    try:
        cursor = connection.cursor()
        query = f"SELECT ClassID FROM Classes WHERE ClassName = '{rombel}'"
        cursor.execute(query)
        result = cursor.fetchone()
        if result:
            return result[0]
        else:
            print(f"Class not found for rombel: {rombel}")
            return None
    except Error as e:
        print(f"Error: {e}")
        return None
    
# Baca data dari file CSV
csv_file_path = 'data_siswa.csv'
with open(csv_file_path, mode='r', encoding='utf-8') as csv_file:
    csv_reader = csv.DictReader(csv_file)
    connection = create_connection()

    if connection:
        for row in csv_reader:
            # Insert data into Users table
            insert_into_users(
                connection,
                row['nama'].lower().replace(" ", "_"),
                'default_password',
                f"{row['nama'].lower().replace(' ', '_')}@example.com",
                row['nama'],
                row['tanggal_lahir'],
                row['jenis_kelamin'],
                row['tempat_lahir'],
                row['nisn'],
                3,
                None,
                None,
                'Active',
                None,
                'Activated'
            )

            # Get the last inserted UserID
            cursor = connection.cursor()
            cursor.execute("SELECT LAST_INSERT_ID()")
            user_id = cursor.fetchone()[0]

            # Get ClassID based on 'rombel'
            class_id = get_class_id(connection, row['rombel'])

            if class_id is not None:
                # Insert data into Students table
                insert_into_students(
                    connection, 
                    row['nipd'], 
                    row['agama'], 
                    row['nama'], 
                    row['tempat_lahir'], 
                    row['nisn'], 
                    f"{row['nama'].lower().replace(' ', '_')}@example.com", 
                    class_id,  # Use the retrieved ClassID
                    user_id
                )

        connection.close()