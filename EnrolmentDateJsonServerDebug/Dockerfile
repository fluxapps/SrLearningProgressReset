FROM node:14

ENV ENROLMENT_DATE_JSON_SERVER_DEBUG_DATA_PATH /enrolment_date_json_server_debug/enrolment_date_json_server_debug_data.json

COPY . /app

WORKDIR /app

RUN rm -rf .git

RUN yarn

WORKDIR ./src

CMD ./server.mjs

EXPOSE 80

VOLUME /enrolment_date_json_server_debug
