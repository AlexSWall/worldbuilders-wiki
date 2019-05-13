import React, { Component } from 'react';

/** === Regular Expression Syntax Rules ===
 *
 * For testing: https://regex101.com/
 *
 * The following should be escaped if one is trying to match the character:
 *     \ ^ . $ | ( ) [ ] * + ? { } ,
 * 
 * == Special Character Definitions ==
 *   \ Quote the next metacharacter
 *   ^ Match the beginning of the line
 *   . Match any character (except newline)
 *   $ Match the end of the line (or before newline at the end)
 *   | Alternation
 *   () Grouping
 *   [] Character class
 *   * Match 0 or more times
 *   + Match 1 or more times
 *   ? Match 1 or 0 times
 *   {n} Match exactly n times
 *   {n,} Match at least n times
 *   {n,m} Match at least n but not more than m times
 *
 * == More Special Character Stuff ==
 *   \t tab (HT, TAB)
 *   \n newline (LF, NL)
 *   \r return (CR)
 *   \f form feed (FF)
 *   \a alarm (bell) (BEL)
 *   \e escape (think troff) (ESC)
 *   \033 octal char (think of a PDP-11)
 *   \x1B hex char
 *   \c[ control char
 *   \l lowercase next char (think vi)
 *   \u uppercase next char (think vi)
 *   \L lowercase till \E (think vi)
 *   \U uppercase till \E (think vi)
 *   \E end case modification (think vi)
 *   \Q quote (disable) pattern metacharacters till \E
 *
 * == Even More Special Characters ==
 *   \w Match a "word" character (alphanumeric plus "_")
 *   \W Match a non-word character
 *   \s Match a whitespace character
 *   \S Match a non-whitespace character
 *   \d Match a digit character
 *   \D Match a non-digit character
 *   \b Match a word boundary
 *   \B Match a non-(word boundary)
 *   \A Match only at beginning of string
 *   \Z Match only at end of string, or before newline at the end
 *   \z Match only at end of string
 *   \G Match only where previous m//g left off (works only with /g)
 */

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