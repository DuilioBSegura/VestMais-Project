# ==============================================================================
# Vest+ Platform - Dockerfile de Produção (PHP 8.3 + Apache Otimizado)
# ==============================================================================

FROM php:8.3-apache

# Metadados da imagem
LABEL maintainer="Equipe Vest+ <contato@vestmais.com.br>"
LABEL description="Imagem otimizada de produção para a plataforma Vest+ (PHP 8.3 + Apache)"
LABEL version="1.0.0"

# Atualiza repositórios do Debian e instala dependências utilitárias
RUN apt-get update && apt-get install -y --no-install-recommends \
    libzip-dev \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Instala e habilita extensões essenciais do PHP (PDO e PDO_MySQL)
RUN docker-php-ext-install pdo pdo_mysql

# Habilita módulos essenciais do Apache (mod_rewrite para rotas e mod_headers para segurança)
RUN a2enmod rewrite headers

# Configura o DocumentRoot do Apache para apontar exclusivamente para /var/www/html/public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Configuração de segurança de diretório no Apache
RUN printf '<Directory /var/www/html/public>\n\
    Options -Indexes +FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>\n' > /etc/apache2/conf-available/vestmais-security.conf \
    && a2enconf vestmais-security

# Aplica php.ini de produção e configurações estritas de segurança
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

RUN printf 'expose_php = Off\n\
display_errors = Off\n\
display_startup_errors = Off\n\
log_errors = On\n\
error_reporting = E_ALL\n\
memory_limit = 256M\n\
upload_max_filesize = 20M\n\
post_max_size = 25M\n\
max_execution_time = 60\n\
date.timezone = America/Sao_Paulo\n' > /usr/local/etc/php/conf.d/production-security.ini

# Define diretório de trabalho padrão
WORKDIR /var/www/html

# Copia os arquivos do projeto para o container
COPY . /var/www/html/

# Ajusta propriedade e permissões para o usuário padrão do Apache (www-data)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expõe a porta HTTP padrão do Apache
EXPOSE 80

# Comando de inicialização do Apache em primeiro plano
CMD ["apache2-foreground"]
