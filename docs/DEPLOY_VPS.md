# Guia de Deploy em VPS Linux (Debian) - Vest+

Este documento orienta o deploy completo da plataforma **Vest+** em uma VPS rodando Debian 11/12 com Docker e Docker Compose.

---

## 1. Pré-requisitos na VPS Debian

Atualize o sistema e instale os pacotes necessários:

```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y curl git ufw gzip
```

### Instalação do Docker e Docker Compose

```bash
# Instalação do Docker oficial
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Adicionar usuário ao grupo docker (evita uso de sudo para comandos docker)
sudo usermod -aG docker $USER
newgrp docker

# Teste de instalação
docker --version
docker compose version
```

---

## 2. Clonagem do Repositório e Configuração de Ambiente

1. Clone o repositório na VPS (ex.: `/var/www/VestMais-Project`):
   ```bash
   cd /var/www
   git clone https://github.com/DuilioBSegura/VestMais-Project.git
   cd VestMais-Project
   ```

2. Crie o arquivo `.env` a partir do modelo de produção:
   ```bash
   cp .env.example .env
   nano .env
   ```

3. Defina senhas seguras no `.env`:
   ```dotenv
   APP_PORT=8080
   APP_ENV=production

   DB_HOST=db
   DB_PORT=3306
   DB_NAME=vestplus
   DB_USER=vestuser
   DB_PASS=SuaSenhaForteAqui123!
   DB_ROOT_PASS=RootSenhaUltraSegura2026!
   DB_CHARSET=utf8mb4
   ```

---

## 3. Subida dos Containers

Execute o build da imagem do Apache/PHP e suba os serviços em segundo plano:

```bash
docker compose up -d --build
```

### Verificação do Status

```bash
# Verifica se os containers estão rodando e saudáveis (healthy)
docker compose ps

# Visualização de logs em tempo real
docker compose logs -f
```

---

## 4. Teste de Acesso e Isolamento

- **Acesso HTTP:** Acesse via navegador `http://SEU_IP_VPS:8080`
- **Validação de Isolamento:**
  - A porta `3306` do MariaDB **não** está exposta publicamente.
  - Apenas o serviço web (`app`) consegue alcançar o banco através da rede interna `vestmais_net`.
  - Pastas internas como `/src`, `/config` e `/database` são inacessíveis via navegador, pois o DocumentRoot aponta estritamente para `/var/www/html/public`.

---

## 5. Automação de Backup e Política de Retenção (7 Dias)

O script `scripts/backup_db.sh` realiza o dump consistente via `mariadb-dump`, comprime em `.sql.gz` com timestamp e remove automaticamente backups mais antigos que 7 dias.

1. Conceda permissão de execução:
   ```bash
   chmod +x scripts/backup_db.sh
   ```

2. Teste a execução manual:
   ```bash
   ./scripts/backup_db.sh
   ```
   Verifique o arquivo gerado na pasta `backups/`:
   ```bash
   ls -lh backups/
   ```

3. Agende no `cron` da VPS para execução diária (exemplo: às 03:00 da manhã):
   ```bash
   crontab -e
   ```
   Adicione a linha:
   ```cron
   0 3 * * * /var/www/VestMais-Project/scripts/backup_db.sh >> /var/www/VestMais-Project/backups/backup.log 2>&1
   ```

---

## 6. Comandos Úteis para Manutenção

| Ação | Comando |
| :--- | :--- |
| **Parar a aplicação** | `docker compose down` |
| **Reiniciar containers** | `docker compose restart` |
| **Recriar containers após atualização de código** | `git pull && docker compose up -d --build` |
| **Acessar o terminal do PHP/Apache** | `docker compose exec app bash` |
| **Acessar o console do MariaDB** | `docker compose exec db mariadb -uvestuser -p vestplus` |
| **Restaurar um backup** | `zcat backups/vestplus_backup_XXXXXX.sql.gz \| docker compose exec -T db mariadb -uvestuser -p vestplus` |
