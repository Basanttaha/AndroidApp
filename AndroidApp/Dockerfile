# استخدام صورة PHP الرسمية
FROM php:8.3-cli  

# تثبيت Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# تعيين مسار العمل داخل الحاوية
WORKDIR /var/www/html

# نسخ ملفات المشروع إلى الحاوية
COPY . .

# تثبيت مكتبات Composer إذا كان هناك composer.json
RUN if [ -f "composer.json" ]; then composer install; fi

# تشغيل سيرفر PHP الداخلي
CMD ["php", "-S", "0.0.0.0:10000"]
