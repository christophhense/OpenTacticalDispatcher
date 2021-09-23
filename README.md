OpenTacticalDispatcher
---

OpenTacticalDispatcher is a software for dispatch tactical units via webbrowser. The focus is of the capability to work with local area networks in off-grid situations. The aim of the project is to make software available under a open source license in order to enable a technical demonstration, easy installation and use. The software is platform-independent because it uses php, JavaScript and HTML5. It can be connected to other systems via a REST API.

Requirements

MySQL- or mariaDB-Database. Webserver with PHP. The following PHP modules may need to be additionally installed: bz2, gd2, mbstring, openssl, pdo_mysql. On the client side Firefox or Chrome as web browser.

Installation

1. Copy the archive file to the directory of a web server, e.g. Lighttpd or Apache.
2. Unzip the archive file and adjust the directory name and the file permissions according to your needs. To save the credentials of the database, the file must be ./incs/db_credentials.inc.php writable by the webserver.
3. Open a browser, e.g. Firefox or Chrome, type 'localhost/[if necessary, path to the folder/]install.php' in the address bar and press Enter.
4. Enter the login data to a MySQL-compatible database, e.g. MariaDB or MySQL.
5. Select the language and click on 'Install'.
6. Once the installation is complete, you can launch the application with a click on 'Start Application'.
7. When you first start, you can choose whether they want to use the configuration wizard.
8. If you do not want to use the configuration wizard or have completed it successfully, You should see the following login page. The default username and passwort is 'admin'.
9. The access to the file install.php should be prevented. E.g. by deleting the file or adjust the access rights.

License

The project is licensed under GPLv3, see LICENSE file in the root directory of the project. In library-licenses-overview.txt you find an overwiew of used librarys and their licenses. License texts of the libraries used placed in the folder next to the respective library.

Information

More information is available at https://www.onetwoserve.net (Website in German language)