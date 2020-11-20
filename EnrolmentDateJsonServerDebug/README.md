# EnrolmentDateJsonServerDebug

Build/rebuild local image with `./build.sh`

Add to your ILIAS `docker-compose.yml`:
```yaml
...
  enrolment_date_json_server_debug:
    image: enrolment_date_json_server_debug
    volumes:
      - ./enrolment_date_json_server_debug:/enrolment_date_json_server_debug
    restart: always
  ...
```

Start/recreate:
`docker-compose up -d enrolment_date_json_server_debug`

Plugin config url: `http://enrolment_date_json_server_debug`
