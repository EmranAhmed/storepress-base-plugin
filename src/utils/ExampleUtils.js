const Meta = {
	states: null,
	init() {
		document
			.querySelector( '#user' )
			.addEventListener( 'change', this.customer_user );
	},

	customer_user( event ) {
		this.load_billing( true, event );
	},

	load_billing( force ) {
		this.states = force;
	},
};

Meta.init();
