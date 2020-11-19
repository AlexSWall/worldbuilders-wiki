import React, { Component } from 'react';

class WikiPanel extends Component 
{
	constructor(props)
	{
		super(props);
	}

	render() {
		return (
			<React.Fragment>
				<h1>{this.props.title}</h1>
				<div dangerouslySetInnerHTML={ {__html: this.props.html} } />
			</React.Fragment>
		);
	}
}

export default WikiPanel;