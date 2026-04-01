FROM php:8.2-fpm-alpine

# 安装系统依赖
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    libxml2-dev \
    zip \
    unzip \
    oniguruma-dev \
    autoconf \
    gcc \
    g++ \
    make \
    linux-headers \
    libzip-dev

# 安装 PHP 扩展（使用已有的扩展安装工具）
RUN docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    mbstring \
    zip \
    exif \
    pcntl \
    bcmath \
    sockets \
    opcache

# 安装 imagemagick 支持
RUN apk add --no-cache imagemagick imagemagick-dev \
    && pecl install imagick \
    && docker-php-ext-enable imagick

# 安装 Redis 扩展 (可选)
RUN pecl install redis && docker-php-ext-enable redis

# 安装 Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 设置工作目录
WORKDIR /var/www/html

# 复制 php.ini 配置
RUN echo "memory_limit = 256M" > /usr/local/etc/php/conf.d/custom.ini \
    && echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "upload_max_filesize = 100M" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "post_max_size = 100M" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "date.timezone = Asia/Shanghai" >> /usr/local/etc/php/conf.d/custom.ini

# 设置目录权限
RUN chown -R www-data:www-data /var/www/html

# 暴露端口
EXPOSE 9000

# 启动命令
CMD ["php-fpm"]
