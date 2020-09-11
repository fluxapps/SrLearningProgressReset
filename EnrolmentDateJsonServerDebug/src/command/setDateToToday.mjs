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

    const current_time = Date.now();

    const ok = await data.setDate(object_id, user_id, current_time);

    res.json({
        ok
    });
}

/**
 * @type {string}
 */
const rest_url = "set_date_to_today";

export {http_method, request, rest_url};
