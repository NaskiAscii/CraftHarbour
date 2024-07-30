 
#!/usr/bin/env python3

import os
import subprocess
import sys
import getpass
import random
import string

def run_command(command):
    process = subprocess.Popen(command, stdout=subprocess.PIPE, stderr=subprocess.PIPE, shell=True)
    output, error = process.communicate()
    if process.returncode != 0:
        print(f"Error executing command: {command}")
        print(error.decode('utf-8'))
        sys.exit(1)
    return output.decode('utf-8')

def generate_password(length=16):
    characters = string.ascii_letters + string.digits + string.punctuation
    return ''.join(random.choice(characters) for i in range(length))

def main():
    print("CraftHarbor Installation Script")
    print("===============================")

    # Check if running as root
    if os.geteuid() != 0:
        print("This script must be run as root. Please use sudo.")
        sys.exit(1)

    # Update system
    print("Updating system...")
    run_command("apt-get update && apt-get upgrade -y")

    # Install required packages
    print("Installing required packages...")
    run_command("apt-get install -y apache2 php libapache2-mod-php php-mysql mysql-server php-curl php-gd php-mbstring php-xml php-xmlrpc openjdk-17-jre-headless screen")

    # Secure MySQL installation
    print("Securing MySQL installation...")
    mysql_root_password = generate_password()
    run_command(f"mysql -e \"ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '{mysql_root_password}';\"")
    run_command("mysql_secure_installation")

    # Create database and user
    print("Creating database and user...")
    db_name = "craftharbor"
    db_user = "craftharbor_user"
    db_password = generate_password()
    run_command(f"mysql -u root -p{mysql_root_password} -e \"CREATE DATABASE {db_name}; CREATE USER '{db_user}'@'localhost' IDENTIFIED BY '{db_password}'; GRANT ALL PRIVILEGES ON {db_name}.* TO '{db_user}'@'localhost'; FLUSH PRIVILEGES;\"")

    # Configure Apache
    print("Configuring Apache...")
    run_command("a2enmod rewrite")
    apache_config = f"""
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/html/craftharbor
    <Directory /var/www/html/craftharbor>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    ErrorLog ${{APACHE_LOG_DIR}}/error.log
    CustomLog ${{APACHE_LOG_DIR}}/access.log combined
</VirtualHost>
"""
    with open("/etc/apache2/sites-available/craftharbor.conf", "w") as f:
        f.write(apache_config)
    run_command("a2ensite craftharbor.conf")
    run_command("a2dissite 000-default.conf")
    run_command("systemctl restart apache2")

    # Move project files
    print("Moving project files...")
    run_command("mkdir -p /var/www/html/craftharbor")
    run_command("cp -R * /var/www/html/craftharbor/")
    run_command("chown -R www-data:www-data /var/www/html/craftharbor")

    # Update config file
    print("Updating configuration file...")
    config_file = "/var/www/html/craftharbor/includes/config.php"
    with open(config_file, "r") as f:
        config_content = f.read()
    config_content = config_content.replace("your_db_user", db_user)
    config_content = config_content.replace("your_db_password", db_password)
    config_content = config_content.replace("craftharbor", db_name)
    with open(config_file, "w") as f:
        f.write(config_content)

    # Create Minecraft directory
    print("Creating Minecraft directory...")
    run_command("mkdir -p /opt/minecraft")
    run_command("chown www-data:www-data /opt/minecraft")

    print("\nInstallation complete!")
    print(f"MySQL root password: {mysql_root_password}")
    print(f"CraftHarbor database name: {db_name}")
    print(f"CraftHarbor database user: {db_user}")
    print(f"CraftHarbor database password: {db_password}")
    print("\nPlease save these credentials in a secure location.")
    print("You can now access your CraftHarbor installation at http://your_server_ip/")

if __name__ == "__main__":
    main()
