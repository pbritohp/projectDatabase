import mysql.connector
from datetime import datetime, timedelta
import smtplib
from email.mime.text import MIMEText

def enviar_email(sender, receiver, subject, body):
    message = f"""\
Subject: {subject}
To: {receiver}
From: {sender}

{body}
"""

    with smtplib.SMTP("sandbox.smtp.mailtrap.io", 2525) as server:
        server.starttls()
        server.login("b5ad514f1a13a1", "********2acd")
        server.sendmail(sender, receiver, message)

conn = mysql.connector.connect(
    host="localhost",
    user="root",
    password="admin123",
    database="SC2C"
)

if conn.is_connected():
    cursor = conn.cursor()

    data_limite = datetime.now() - timedelta(days=5*365)
    query = "UPDATE Project SET sit_project_id = 2 WHERE date_in < %s RETURNING id_Laboratory, pName"
    cursor.execute(query, (data_limite,))
    inativados = cursor.fetchall()

    for projeto in inativados:
        id_laboratorio, nome_projeto = projeto

        query_lab = "SELECT email FROM laboratory WHERE idLab = %s"
        cursor.execute(query_lab, (id_laboratorio,))
        resultado = cursor.fetchone()

        if resultado:
            email_laboratorio = resultado[0]
            enviar_email('sc2c@sc2c.com', email_laboratorio, f'Projeto Inativado: {nome_projeto}',
                         f'O projeto {nome_projeto} foi inativado. Verifique a situação do projeto.')
        else:
            print(f"E-mail do laboratório (ID {id_laboratorio}) não encontrado.")

    conn.commit()
    cursor.close()
    conn.close()

else:
    print("Não foi possível conectar ao banco de dados.")
