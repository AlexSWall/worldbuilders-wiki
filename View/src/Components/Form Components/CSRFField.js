import React, { Component } from 'react';

class CSRFField extends Component 
{
	render() {
		return (
			<div dangerouslySetInnerHTML={{ __html: this.props.csrfHTML }} />
		);
	}
}

export default CSRFField;