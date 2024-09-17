let url = 'http://127.0.0.1:8000';
if(process.env.NODE_ENV != 'development'){
	url = 'http://zec.localhost.com/index.php/api';
}
export default {
	url
}
