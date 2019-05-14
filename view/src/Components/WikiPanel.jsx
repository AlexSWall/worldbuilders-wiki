import React, { Component } from 'react';

class WikiPanel extends Component 
{
	constructor()
	{
		super();
		this.state = {
			pageName: '',
			title: '',
			wikiContent: ''
		}
	}

	componentDidMount = () =>
	{
		window.addEventListener("hashchange", this.onHashChange, false);
		this.onHashChange(); /* Run for initial hash */
	};

	onHashChange = () =>
	{
		const hash = window.location.hash.substring(1);
		const [webpageName, heading] = hash.split('#');

		if ( webpageName === '' )
			window.location.hash = 'Home'; /* This will result in this function being called again. */
		else
			this.updatePageContents(webpageName, heading);
	};

	updatePageContents = (webpageName, heading) =>
	{
		fetch(`http://localhost:8080/w/${webpageName}`)
			.then(res => res.json())
			.then(
				(response) => {
					this.setState({
						pageName: response.webpageName,
						title: response.webpageTitle,
						wikiContent: response.webpageHTML
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
						pageName: '',
						title: '',
						wikiContent: ''
					});
				}
			);
	};

	render() {
		document.title = this.state.title;
		return (
			<React.Fragment>
				{this.state.title && <h1>{this.state.title}</h1>}
				<div dangerouslySetInnerHTML={ {__html: this.state.wikiContent} } />
			</React.Fragment>
		);
	}
}

export default WikiPanel;