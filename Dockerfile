FROM php:7.2-apache

RUN  mkdir /tmp/certs
RUN chown www-data:www-data /tmp/certs
	
COPY hacienda /var/www/html/hacienda/
COPY xmlseclibs /var/www/html/xmlseclibs/
COPY signer.php /var/www/html/
COPY cert-uploader.php /var/www/html/

ENV CERTIFICATE_UPLOAD false

EXPOSE 80

# To build the image:
# docker build -t php-firmador .


# Running examples:
#
# Run with local directory mounted in /tmp/certs so no need to enable CERTIFICATE_UPLOAD and exposing port in localhost 8081
# docker run --rm -d -v /home/user/certificates:/tmp/certs -p 8081:80  php-firmador:latest
# docker run --rm -d --mount type=bind,source=/home/user/certs,destination=/tmp/certs -p 8081:80 php-firmador:latest

# Run with CERTIFICATE_UPLOAD and docker's ip address pool
# in this case you have to upload the certificate prior to signing 

# docker run --rm -d  -p 8081:80 -e CERTIFICATE_UPLOAD=true php-firmador:latest
