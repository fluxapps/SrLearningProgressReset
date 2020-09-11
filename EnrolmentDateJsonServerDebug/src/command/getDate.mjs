/**
 * @type {string}
 */
const http_method = "post";

/**
 * @param {Data} data
 * @param {function} req
 * @param {function} res
 *
 * @returns {Promise<void>}
 */
async function request(data, req, res) {
    const {object_id, user_id} = req.body;

    const date = new Date(await data.getDate(object_id, user_id)).toJSON().split("T")[0];

    res.json({
        date
    });
}

/**
 * @type {string}
 */
const rest_url = "get_date";

export {http_method, request, rest_url};
