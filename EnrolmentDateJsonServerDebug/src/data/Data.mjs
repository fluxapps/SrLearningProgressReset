import {readFile, writeFile} from "fs/promises";

/**
 * Class Data
 */
class Data {
    /**
     * Data constructor
     *
     * @param {string} path
     */
    constructor(path) {
        /**
         * @type {string}
         *
         * @private
         */
        this._path = path;
    }

    /**
     * @param {number} object_id
     * @param {number} user_id
     *
     * @returns {Promise<number|null>}
     */
    async getDate(object_id, user_id) {
        const data = await this._readData();

        if (!data[object_id]) {
            return null;
        }

        if (!data[object_id][user_id]) {
            return null;
        }

        return data[object_id][user_id];
    }

    /**
     * @param {number} object_id
     * @param {number} user_id
     * @param {number} time
     *
     * @returns {Promise<boolean>}
     */
    async setDate(object_id, user_id, time) {
        let ok;

        try {
            const data = await this._readData();

            if (!data[object_id]) {
                data[object_id] = {};
            }

            data[object_id][user_id] = time;

            await this._writeData(data);

            ok = true;
        } catch (err) {
            console.error(err);

            ok = false;
        }

        return ok;
    }

    /**
     * @returns {Promise<Object>}
     *
     * @private
     */
    async _readData() {
        let data;

        try {
            data = JSON.parse(await readFile(this._path, "utf8"));
        } catch (err) {
            console.error(err);
        }

        if (!data) {
            data = {};
        }

        return data;
    }

    /**
     * @param {Object} data
     *
     * @returns {Promise<void>}
     *
     * @private
     */
    async _writeData(data) {
        await writeFile(this._path, JSON.stringify(data, null, 2), "utf8");
    }
}

export {Data};
