let app = new Vue({
	el:'#app',
	data:{
		codigoBarra:""
	},
	methods:{
		async find(){
			ruta="db/activar.php?codigoBarra="+this.codigoBarra
			console.log(ruta)
			let res = await fetch(ruta)
			let data = await res.json()
			console.log(res)
		}
	}
})
