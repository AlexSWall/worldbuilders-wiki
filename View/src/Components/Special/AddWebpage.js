import React, { Component } from 'react';

class AddWebpage extends Component 
{
	render() {
		return (
			<React.Fragment>
				<p>For help with writing in the wiki templating style, see <a href="/#Special:Template_Formatting">Template Formatting</a>.</p>

				<form action="" method="post">
					<label htmlFor="page_name">Page Name: <input type="text" name="page_name" id="page_name"/> </label>
					<label htmlFor="webpage">Add the webpage text here:</label>
					<textarea id="webpage" name="webpage" rows="3" cols="40"></textarea>
					<input type="submit" value="Add"/>
				</form>
			</React.Fragment>
		);
	}
}

export default AddWebpage;