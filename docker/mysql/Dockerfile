FROM ubuntu:bionic

RUN apt-get update \
    && DEBIAN_FRONTEND=noninteractive apt-get install -yqq --no-install-recommends \
    mysql-server \
    && rm -rf /var/log/mysql/* \
    && rm -rf /var/lib/mysql \
    && mkdir -p /var/lib/mysql /var/run/mysqld \
    && chown -R mysql:mysql /var/lib/mysql /var/run/mysqld \
    && chmod 777 /var/run/mysqld \
    && rm -rf /var/lib/apt/lists/*

COPY image/ /

RUN find /etc/mysql/ -name '*.cnf' -print0 \
    | xargs -0 grep -lZE '^(bind-address|log)' \
    | xargs -rt -0 sed -Ei 's/^(bind-address|log)/#&/'

EXPOSE 3306/tcp

WORKDIR /var/lib/mysql

VOLUME /var/lib/mysql

ENTRYPOINT ["/usr/local/sbin/docker-entrypoint.sh"]

CMD ["/usr/sbin/mysqld"]
