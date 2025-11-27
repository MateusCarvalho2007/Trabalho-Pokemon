import sys
import json
import smtplib
from email.message import EmailMessage

def enviar_email(destinatario, nome_dest, assunto, mensagem):
    """Função que envia email com dados recebidos do PHP."""

    meu_email = "svaagisifrs@gmail.com"
    minha_senha = "lxff suay qplv ecaf"

    try:
        with smtplib.SMTP('smtp.gmail.com', 587) as smtp:
            smtp.starttls()
            smtp.login(meu_email, minha_senha)

            msg = EmailMessage()
            msg["Subject"] = assunto
            msg["From"] = meu_email
            msg["To"] = destinatario
            msg.set_content(f"Olá {nome_dest}!\n\n{mensagem}")

            smtp.send_message(msg)
            print("OK")  # PHP vai checar essa resposta

    except Exception as e:
        print(f"ERRO: {str(e)}")


# --------- Receber dados do PHP ---------
dados = json.load(sys.stdin)

enviar_email(
    destinatario=dados["email"],
    nome_dest=dados["nome"],
    assunto=dados["assunto"],
    mensagem=dados["mensagem"]
)
