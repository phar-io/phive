FROM fedora:37

RUN dnf install -y php-cli php-xml php-curl php-mbstring gnupg which

ADD ./build/phar/phive*.phar ./opt/phario/phive
RUN chmod +x ./opt/phario/phive
RUN mkdir /repo

WORKDIR /repo

ENV PHIVE_HOME=/home

ENTRYPOINT ["/opt/phario/phive"]
