FROM fedora:37

LABEL stage=phive-fedora-build

RUN dnf install -y php-cli php-xml php-curl php-mbstring gnupg which busybox

ADD build/phar/phive*.phar /usr/local/bin/phive
RUN chmod +x /usr/local/bin/phive
RUN mkdir /repo
RUN mkdir /phive
RUN ln -sf /usr/sbin/busybox /bin/sh
RUN mkdir /github && ln -s /repo /github/workspace

ENV PHIVE_HOME=/phive
ENV HOME=/phive

WORKDIR /repo

ENTRYPOINT ["/usr/local/bin/phive"]
