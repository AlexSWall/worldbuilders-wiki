import React, { Component } from 'react';

import WikiTemplateEngine from 'Utilities/WikiTemplateEngine';

class WikiPanel extends Component 
{
	render() {
		return (
			<React.Fragment>
				<div dangerouslySetInnerHTML={ {__html: WikiTemplateEngine.parseWebpage(this.props.rawWebpageContent)} } />
			</React.Fragment>
		);
	}
}

export default WikiPanel;