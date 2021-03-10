FROM node:15-alpine

WORKDIR /app

COPY package.json yarn.lock ./
ENV NODE_ENV production
RUN yarn

COPY . .

WORKDIR bin

EXPOSE 80

RUN chmod +x server.mjs
ENTRYPOINT ["./server.mjs"]

ENV ENROLMENT_DATE_JSON_SERVER_DEBUG_DATA_PATH /enrolment_date_json_server_debug/enrolment_date_json_server_debug_data.json
VOLUME /enrolment_date_json_server_debug
