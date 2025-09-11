#!/bin/bash

# Self-signed SSL certificate generator for development
# This creates a self-signed certificate for testing PWA in HTTPS environment

SSL_DIR="/etc/apache2/ssl"
DOMAIN="localhost"
DAYS=365

echo "Creating self-signed SSL certificate for development..."

# Create SSL directory
sudo mkdir -p $SSL_DIR

# Generate private key
echo "Generating private key..."
sudo openssl genrsa -out $SSL_DIR/aimedkorea.key 2048

# Generate certificate signing request
echo "Generating certificate signing request..."
sudo openssl req -new -key $SSL_DIR/aimedkorea.key -out $SSL_DIR/aimedkorea.csr -subj "/C=KR/ST=Seoul/L=Seoul/O=AimedKorea/OU=Development/CN=$DOMAIN"

# Generate self-signed certificate
echo "Generating self-signed certificate..."
sudo openssl x509 -req -days $DAYS -in $SSL_DIR/aimedkorea.csr -signkey $SSL_DIR/aimedkorea.key -out $SSL_DIR/aimedkorea.crt

# Set proper permissions
sudo chmod 600 $SSL_DIR/aimedkorea.key
sudo chmod 644 $SSL_DIR/aimedkorea.crt

echo "SSL certificate created successfully!"
echo "Certificate location: $SSL_DIR/aimedkorea.crt"
echo "Private key location: $SSL_DIR/aimedkorea.key"

# Enable Apache SSL module
echo "Enabling Apache SSL module..."
sudo a2enmod ssl
sudo a2enmod rewrite
sudo a2enmod headers

# Create SSL virtual host configuration
echo "Creating SSL virtual host configuration..."
sudo tee /etc/apache2/sites-available/aimedkorea-ssl.conf > /dev/null <<EOF
<VirtualHost *:443>
    ServerName localhost
    DocumentRoot /var/www/html/aimedkorea/public

    SSLEngine on
    SSLCertificateFile $SSL_DIR/aimedkorea.crt
    SSLCertificateKeyFile $SSL_DIR/aimedkorea.key

    # Strong SSL Protocol Settings
    SSLProtocol -all +TLSv1.2 +TLSv1.3
    SSLCipherSuite ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384
    SSLHonorCipherOrder on

    # Headers for PWA
    Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-Content-Type-Options "nosniff"

    <Directory /var/www/html/aimedkorea/public>
        AllowOverride All
        Require all granted
        
        # Enable Service Worker scope
        <Files "service-worker.js">
            Header set Service-Worker-Allowed "/"
        </Files>
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/aimedkorea_ssl_error.log
    CustomLog \${APACHE_LOG_DIR}/aimedkorea_ssl_access.log combined
</VirtualHost>
EOF

# Enable the SSL site
echo "Enabling SSL site..."
sudo a2ensite aimedkorea-ssl.conf

# Update the non-SSL site to redirect to HTTPS
echo "Updating HTTP site to redirect to HTTPS..."
sudo tee /etc/apache2/sites-available/aimedkorea.conf > /dev/null <<EOF
<VirtualHost *:80>
    ServerName localhost
    DocumentRoot /var/www/html/aimedkorea/public

    # Redirect all HTTP traffic to HTTPS
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

    <Directory /var/www/html/aimedkorea/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/aimedkorea_error.log
    CustomLog \${APACHE_LOG_DIR}/aimedkorea_access.log combined
</VirtualHost>
EOF

# Reload Apache
echo "Reloading Apache..."
sudo systemctl reload apache2

echo ""
echo "========================================="
echo "Self-signed SSL certificate setup complete!"
echo "========================================="
echo ""
echo "You can now access your site via:"
echo "https://localhost"
echo ""
echo "Note: Browsers will show a security warning because this is a self-signed certificate."
echo "This is normal for development. Click 'Advanced' and 'Proceed to localhost' to continue."
echo ""
echo "For production, use Let's Encrypt with a real domain name."