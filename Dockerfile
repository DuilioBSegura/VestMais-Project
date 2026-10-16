# ==============================================================================
# PROJETO: Vest+ - Plataforma de CMS e Banco de Questões
# ARQUIVO: Dockerfile
# FINALIDADE: Build da imagem PHP 8.3 + Apache otimizada para Produção
# ==============================================================================

FROM php:8.3-apache

# Metadados da imagem
LABEL maintainer="Vest+ Team <contato@vestplus.com.br>"
LABEL description="Imagem Apache + PHP 8.3 de produção para a plataforma Vest+"

# 1. Instalação e ativação das extensões PDO e PDO MySQL
RUN docker-php-ext-install pdo pdo_mysql

# 2. Habilitação dos módulos necessários do Apache (rewrite para Front Controller e headers para segurança)
RUN a2enmod rewrite headers

# 3. Alteração do DocumentRoot do Apache para /var/www/html/public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 4. Habilitação do AllowOverride All para leitura do .htaccess e hardening de assinaturas do Apache
RUN { \
    echo '<Directory /var/www/html/public>'; \
    echo '    Options -Indexes +FollowSymLinks'; \
    echo '    AllowOverride All'; \
    echo '    Require all granted'; \
    echo '</Directory>'; \
} > /etc/apache2/conf-available/vestplus.conf \
    && a2enconf vestplus \
    && echo "ServerTokens Prod" >> /etc/apache2/apache2.conf \
    && echo "ServerSignature Off" >> /etc/apache2/apache2.conf

# 5. Configuração de segurança do PHP em ambiente de produção
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

RUN { \
    echo 'expose_php = Off'; \
    echo 'display_errors = Off'; \
    echo 'display_startup_errors = Off'; \
    echo 'log_errors = On'; \
    echo 'error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT'; \
    echo 'error_log = /var/log/apache2/php_error.log'; \
    echo 'memory_limit = 256M'; \
    echo 'upload_max_filesize = 32M'; \
    echo 'post_max_size = 32M'; \
    echo 'date.timezone = America/Sao_Paulo'; \
} > "$PHP_INI_DIR/conf.d/vestplus-security.ini"

# 6. Definição do diretório de trabalho
WORKDIR /var/www/html

# 7. Cópia dos arquivos do projeto
COPY . /var/www/html

# 8. Ajuste de permissões de propriedade e leitura/escrita para www-data
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \; \
    && mkdir -p /var/www/html/logs \
    && chown -R www-data:www-data /var/www/html/logs \
    && chmod -R 775 /var/www/html/logs

# Porta padrão de escuta HTTP
EXPOSE 80

# Inicialização do servidor Apache em primeiro plano
CMD ["apache2-foreground"]
