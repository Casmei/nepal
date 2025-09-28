# Usa uma imagem oficial do PHP 8.2 com FPM
FROM php:8.2-fpm

# Argumentos para UID/GID para evitar problemas de permissão
ARG user=sail
ARG uid=1000

# Instala dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    supervisor # Supervisor para gerenciar o worker (opcional, mas bom para produção)

# Limpa o cache do apt
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instala as extensões PHP necessárias para o Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Cria um usuário para a aplicação para não rodar como root
RUN useradd -G www-data,root -u ${uid} -d /home/${user} ${user}
RUN mkdir -p /home/${user}/.composer && \
    chown -R ${user}:${user} /home/${user}

# Define o diretório de trabalho
WORKDIR /var/www

# Copia o código da aplicação para o container
COPY . .

# Altera o dono dos arquivos para o usuário criado
RUN chown -R ${user}:${user} /var/www

# Muda para o usuário da aplicação
USER ${user}

# Expõe a porta 9000 para o FPM
EXPOSE 9000

# Comando padrão para iniciar o PHP-FPM
CMD ["php-fpm"]