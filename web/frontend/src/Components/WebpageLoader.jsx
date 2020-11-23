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

		window.addEventListener("hashchange", this.onHashChange);
	}

	componentDidMount()
	{
		// After mounting the component, load the content by firing a 'hashchange'
		// event.
		window.dispatchEvent(new HashChangeEvent("hashchange"));
	}

	onHashChange = (_event) =>
	{
		const hash = window.location.hash.substring(1);
		const [webpagePath, heading] = hash.split('#');

		if ( webpagePath === '' )
			// If there is no hash, set it to 'Home'.
			// This will result in this function being called again.
			window.location.hash = 'Home';
		else
			// Otherwise, update the contents by fetching the intended contents,
			// setting the inner component for it, and moving to the heading.
			this.getAndUpdatePageContents(webpagePath, heading);
	};

	getAndUpdatePageContents = (webpagePath, heading) =>
	{
		fetch(this.props.urlBase + webpagePath)
			.then(res => res.json())
			.then(response => {
				const webpageData = response.wikiPage;
				const title = 'Hello!';

				// Set title.
				document.title = title;

				// Set inner React component and its data.
				this.setState({
					childComponent: this.props.componentMapper(webpageData.urlPath),
					webpageData: webpageData
				});

				// Move to heading, if there was one.
				this.moveToHeading(heading);
			});
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
			return <i>Fetching and loading content...</i>;
		return <ChildComponent {...this.state.webpageData} />;
	}
}

export default WebpageLoader;
