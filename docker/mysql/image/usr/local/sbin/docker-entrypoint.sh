#!/bin/bash
set -e

if [ ! -d /var/lib/mysql/mysql ]; then
    echo "Initializing database"
    /usr/sbin/mysqld --initialize-insecure >/dev/null 2>&1
    echo "Database initialized"

    echo "MySQL init process in progress..."
    /usr/sbin/mysqld --skip-networking >/dev/null 2>&1 &
    pid="$!"

    timeout=120
    echo -n "Waiting for MySQL to accept connections..."
    while ! /usr/bin/mysqladmin -u root status >/dev/null 2>&1
    do
        timeout=$(($timeout - 1))
        if [ $timeout -eq 0 ]; then
            echo
            echo >&2 "MySQL init process failed."
            echo
            exit 1
        fi
        sleep 1
    done

    mysql -uroot \
        -e "DELETE FROM mysql.user WHERE user NOT IN ('root') OR host NOT IN ('localhost');"

    if [ ! -z "${MYSQL_DATABASE}" ]; then
        mysql -uroot \
            -e "CREATE DATABASE IF NOT EXISTS \`$MYSQL_DATABASE\`;"
    fi

    if [ ! -z "${MYSQL_USER}" ]; then
        mysql -uroot \
            -e "CREATE USER '$MYSQL_USER'@'%' IDENTIFIED BY '$MYSQL_PASSWORD';"

        if [ ! -z "${MYSQL_DATABASE}" ]; then
            mysql -uroot \
                -e "GRANT ALL ON \`$MYSQL_DATABASE\`.* TO '$MYSQL_USER'@'%';"
        fi
    fi

    if [ ! -z "$MYSQL_ROOT_HOST" -a "$MYSQL_ROOT_HOST" != 'localhost' ]; then
        mysql -uroot \
            -e "CREATE USER 'root'@'${MYSQL_ROOT_HOST}' IDENTIFIED BY '${MYSQL_ROOT_PASSWORD}';"

        mysql -uroot \
            -e "GRANT ALL ON *.* TO 'root'@'${MYSQL_ROOT_HOST}' WITH GRANT OPTION;"
    fi

    for f in /docker-entrypoint-initdb.d/*; do
        case "$f" in
            *.sh) echo "running $f"; . "$f" ;;
            *.sql) echo "running $f"; mysql -uroot "${MYSQL_DATABASE}" < "$f"; echo ;;
            *.sql.gz) echo "running $f"; gunzip -c "$f" | mysql -uroot "${MYSQL_DATABASE}"; echo ;;
            *) echo "ignoring $f" ;;
        esac
        echo
    done

    mysql -uroot \
        -e "SET PASSWORD FOR 'root'@'localhost'=PASSWORD('${MYSQL_ROOT_PASSWORD}'); FLUSH PRIVILEGES;"

    if ! kill -s TERM "$pid" || ! wait "$pid"; then
        echo
        echo >&2 "MySQL init process failed."
        echo
        exit 1
    fi

    echo
    echo "MySQL init process done. Ready for start up."
    echo
fi

exec "$@"
