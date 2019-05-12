import renderTableOfContents from 'Scripts/generateTableOfContents';

class WikiTemplateEngine
{
	static parseWebpage(rawWebpageContent)
	{
		return renderTableOfContents(rawWebpageContent);
	}
}

export default WikiTemplateEngine;