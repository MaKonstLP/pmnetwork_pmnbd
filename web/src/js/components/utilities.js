function status(response) {
	if (response.status >= 200 && response.status < 300) {
		return Promise.resolve(response)
	} else {
		return Promise.reject(new Error(response.statusText))
	}
}

function json(response) {
	return response.json()
}

function text(response) {
	return response.text()
}

function hash (value) {
	let string = value + '';
	var hash = 0;
	if (string.length == 0) return hash;
	for (let i = 0; i < string.length; i++) {
		let char = string.charCodeAt(i);
		hash = ((hash<<5)-hash)+char;
		hash = hash & hash; // Convert to 32bit integer
	}
	return hash;
}

export {status, json, text, hash}
