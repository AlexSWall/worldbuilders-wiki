import React, { Component } from 'react';

import renderTableOfContents from 'Utilities/TableOfContentsUtils';

class WikiPanel extends Component 
{
	constructor()
	{
		super();
		this.state = {
			pageName: window.location.hash.substring(1),
			wikiContent: ''
		}
	}

	componentDidMount = () =>
	{
		window.addEventListener("hashchange", this.onHashChange, false);
		this.onHashChange();
	};

	onHashChange = () =>
	{
		const hash = window.location.hash.substring(1);
		const [pageName, heading] = hash.split('#');

		if ( pageName === '' )
			window.location.hash = 'Home'; /* This will result in this function being called again. */
		else
			this.updatePageContents(pageName, heading);
	};

	updatePageContents = (pageName, heading) =>
	{
		fetch(`http://localhost:8080/w/${pageName}`)
			.then(res => res.text())
			.then(
				(result) => {
					const title = pageName.replace(/_/g, ' ');
					document.title = title;
					this.setState({
						pageName: pageName,
						wikiContent: result
					});
					if ( heading !== '' )
					{
						const headingElement = document.getElementById(heading);
						if ( headingElement !== null )
							headingElement.scrollIntoView();
					}
				},
				(error) => {
					this.setState({
						wikiContent: ''
					});
				}
			);
	};

	render() {
		const htmlContent = renderTableOfContents(this.state.pageName, this.state.wikiContent);
		return (
			<React.Fragment>
				<div dangerouslySetInnerHTML={ {__html: htmlContent} } />
			</React.Fragment>
		);
	}
}

export default WikiPanel;