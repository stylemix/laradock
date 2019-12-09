FROM nginx:alpine

COPY nginx.conf /etc/nginx/
COPY logrotate/nginx /etc/logrotate.d/
ADD startup.sh /opt/startup.sh

RUN addgroup -g 82 -S www-data ; \
    adduser -u 82 -D -S -G www-data www-data && exit 0 ; exit 1

RUN set -x; \
    apk update && \
    apk upgrade && \
    apk --update add logrotate && \
    apk add --no-cache openssl && \
    apk add --no-cache bash && \
    # Create 'messages' file used from 'logrotate'
    touch /var/log/messages && \
    rm /etc/nginx/conf.d/default.conf && \
    sed -i 's/\r//g' /opt/startup.sh

CMD ["/bin/bash", "/opt/startup.sh"]

EXPOSE 80 443
