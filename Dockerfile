FROM tomsik68/xampp:7

EXPOSE 80

RUN sed -i '1a while [ $? -ne 0 ]; do /opt/lampp/bin/mysql < /opt/lampp/htdocs/install/install/includes/sql.sql; done' /startup.sh
RUN sed -i '1a /opt/lampp/bin/mysql < /opt/lampp/htdocs/install/install/includes/sql.sql' /startup.sh

