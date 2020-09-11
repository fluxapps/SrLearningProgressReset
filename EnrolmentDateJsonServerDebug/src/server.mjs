#!/usr/bin/env node
import bodyParser from "body-parser";
import {createServer} from "http";
import {Data} from "./data/Data.mjs";
import express from "express";
import {readdir} from "fs/promises";

const {env} = process;

(async () => {
    const server = express();

    server.use(bodyParser.json());

    const node_server = createServer(server);

    node_server.listen(80);

    const data = new Data(env.ENROLMENT_DATE_JSON_SERVER_DEBUG_DATA_PATH);

    for (const command of (await readdir("./command", {withFileTypes: true})).filter(command => command.isFile() && command.name.endsWith(".mjs"))) {
        const {http_method, request, rest_url} = await import(`./command/${command.name}`);

        server[http_method](`/${rest_url}`, request.bind(null, data));
    }
})();
