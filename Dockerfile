FROM ghcr.io/nfra-project/kickstart-flavor-php:unstable
ENV DEV_CONTAINER_NAME="schiller"


ENV CONF_PATH=/data
ENV CONF_KEYSTORE_FILE=/data/.keystore.yml


ADD / /opt
RUN ["bash", "-c",  "chown -R user /opt"]
RUN ["/kickstart/run/entrypoint.sh", "build"]

ENTRYPOINT ["/kickstart/run/entrypoint.sh", "standalone"]
