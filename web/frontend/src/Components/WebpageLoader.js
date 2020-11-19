import React, { Component } from 'react';

class WebpageLoader extends Component 
{
	constructor(props)
	{
		super(props);
		this.state = {
			childComponent: undefined,
			webpageData: {}
		}

		window.addEventListener("hashchange", this.onHashChange, false);
	}

	componentDidMount()
	{
		window.dispatchEvent(new HashChangeEvent("hashchange"));
	}

	onHashChange = (event) =>
	{
		let oldHash = event.oldURL;
		let newHash = event.newURL;

		const hash = window.location.hash.substring(1);
		const [webpageName, heading] = hash.split('#');

		if ( webpageName === '' )
			window.location.hash = 'Home'; /* This will result in this function being called again. */
		else
			this.getAndUpdatePageContents(webpageName, heading);
	};

	getAndUpdatePageContents = (webpageName, heading) =>
	{
		fetch(this.props.urlBase + webpageName)
			.then(res => res.json())
			.then(response => {
					document.title = response.wikiPage.title;

					this.setState({
						childComponent: this.props.componentMapper(response.wikiPage.urlPath),
						webpageData: response.wikiPage
					});

					this.moveToHeading(heading);
				}
			);
	};

	moveToHeading(heading)
	{
		if ( heading === '' )
			return;
		const headingElement = document.getElementById(heading);
		if ( headingElement !== null )
			headingElement.scrollIntoView();
	}

	render() {
		const ChildComponent = this.state.childComponent
		if (ChildComponent === undefined)
			return <i>Loading...</i>;
		return <ChildComponent {...this.state.webpageData} />;
	}
}

export default WebpageLoader;