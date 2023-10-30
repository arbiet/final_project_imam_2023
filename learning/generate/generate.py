import random
from faker import Faker
import mysql.connector
import json

# Set up the Faker generator
fake = Faker()

# Connect to the MySQL database
db = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="skripsi-aziz"
)
cursor = db.cursor()

# Generate 3 classes
for class_number in range(1, 4):
    class_name = f"Class {class_number}"
    description = fake.text()

    insert_class_query = f"""
    INSERT INTO class (class_name, description)
    VALUES ('{class_name}', '{description}');
    """
    cursor.execute(insert_class_query)
    db.commit()
# Generate 50 users
for _ in range(50):
    username = fake.user_name()
    email = fake.email()
    password = fake.password()
    role = random.choice(['user', 'guru'])
    fullname = fake.name()
    nisn = fake.random_int(min=1000000000, max=9999999999)
    class_id = random.randint(1, 3)  # Assuming there are 3 classes

    insert_user_query = f"""
    INSERT INTO users (username, email, password, role, fullname, nisn, class_id)
    VALUES ('{username}', '{email}', '{password}', '{role}', '{fullname}', '{nisn}', {class_id});
    """
    cursor.execute(insert_user_query)
    db.commit()


# Generate 2 teachers
for _ in range(2):
    teacher_username = fake.user_name()
    teacher_email = fake.email()
    teacher_password = fake.password()
    teacher_fullname = fake.name()

    insert_teacher_query = f"""
    INSERT INTO users (username, email, password, role, fullname)
    VALUES ('{teacher_username}', '{teacher_email}', '{teacher_password}', 'guru', '{teacher_fullname}');
    """
    cursor.execute(insert_teacher_query)
    db.commit()

# Generate 3 subjects
for _ in range(3):
    subject_name = fake.word()
    subject_description = fake.text()

    insert_subject_query = f"""
    INSERT INTO subjects (subject_name, description)
    VALUES ('{subject_name}', '{subject_description}');
    """
    cursor.execute(insert_subject_query)
    db.commit()

# Generate 10 materials
for subject_id in range(1, 4):
    for sequence in range(1, 11):
        title = fake.sentence()
        material_type = random.choice(['video', 'gambar', 'penjelasan'])
        content = fake.paragraph()

        insert_material_query = f"""
        INSERT INTO materials (subject_id, title, type, content, sequence)
        VALUES ({subject_id}, '{title}', '{material_type}', '{content}', {sequence});
        """
        cursor.execute(insert_material_query)
        db.commit()

# Generate class_subjects relationships
for class_id in range(1, 4):
    for subject_id in range(1, 4):
        insert_class_subject_query = f"""
        INSERT INTO class_subjects (class_id, subject_id)
        VALUES ({class_id}, {subject_id});
        """
        cursor.execute(insert_class_subject_query)
        db.commit()

# Generate post-tests and pre-tests
for subject_id in range(1, 4):
    for sequence in range(1, 11):
        test_title = fake.sentence()
        test_type = random.choice(['pilihan_ganda', 'jawaban_singkat'])
        questions = {
            "question1": {
                "question": "Question1",
                "options": ["Option1", "Option2", "Option3", "Option4"],
                "answer": 1,
                "explanation": "Explanation1"
            },
            "question2": {
                "question": "Question2",
                "options": ["Option1", "Option2", "Option3", "Option4"],
                "answer": 1,
                "explanation": "Explanation2"
            }
        }
        questions_json = json.dumps(questions)  # Convert dictionary to JSON string
        material_id = random.randint(1, 30)  # Assuming there are 30 materials

        insert_post_test_query = f"""
        INSERT INTO post_tests (subject_id, title, type, questions, sequence, material_id)
        VALUES ({subject_id}, '{test_title}', '{test_type}', '{questions_json}', {sequence}, {material_id});
        """
        cursor.execute(insert_post_test_query)
        db.commit()

        # ... Similar code for pre-tests

# Close the database connection
db.close()
