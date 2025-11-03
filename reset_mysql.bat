@echo off
echo Fixing MySQL/MariaDB connection issues...
echo.

REM Stop MySQL service
echo Stopping MySQL service...
"C:\xampp\mysql\bin\mysqladmin.exe" -u root shutdown

REM Wait a moment
timeout /t 5 /nobreak

REM Start MySQL with skip-grant-tables option
echo Starting MySQL with skip-grant-tables...
start /B "MySQL" "C:\xampp\mysql\bin\mysqld.exe" --skip-grant-tables --skip-networking

REM Wait for MySQL to start
timeout /t 5 /nobreak

REM Reset the root password and permissions
echo Resetting MySQL privileges...
"C:\xampp\mysql\bin\mysql.exe" -u root -e "USE mysql; UPDATE user SET plugin='mysql_native_password' WHERE User='root'; FLUSH PRIVILEGES; ALTER USER 'root'@'localhost' IDENTIFIED BY ''; FLUSH PRIVILEGES; GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' WITH GRANT OPTION; GRANT ALL PRIVILEGES ON *.* TO 'root'@'127.0.0.1' WITH GRANT OPTION; FLUSH PRIVILEGES;"

REM Stop MySQL
echo Stopping MySQL service...
"C:\xampp\mysql\bin\mysqladmin.exe" -u root shutdown

echo.
echo Done! Now restart MySQL from XAMPP Control Panel.
pause