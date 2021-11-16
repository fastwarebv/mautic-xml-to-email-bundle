# Mautic XML to E-mail

## [Description](id:description)
The Mautic XmlToEmailBundle is a Mautic plugin that allows you to generate e-mails from a XML-feed.

### Purpose
Send out an e-mail with for example to latest posts of your blog.

### Compatibility
This plugin has been tested with Mautic versions 4.x.

### PHP version
This plugin has been tested with PHP 7.4 and PHP 8.0.

### Features
 * Create a custom template for the items in the XML feed
 * Use images from the feed
 * Unlike RSS-feeds, you can use any property in your XML-feed

## [Installation](id:installation)

1. Download the plugin from github
2. Move / Upload folder to the plugins directory. Name the folder `MauticXmlToEmailBundle`
3. In the Mautic GUI, go to the gear and then to Plugins.
4. Click on the "Install/Upgrade Plugins" button
5. You should now see the "Xml To Email" in your list of plugins.

## [Usage](id:usage)
Use the "raw" component (`<wj-raw>`) of the grapesjs e-mail editor. In the content of the component set to following content:

```
<feed url="<<FEEDURL>>">
	<p>{feed.title}</p>
    <feeditem loop="items">
        <h3>{feeditem.title}</h3>
        <p><small>{feeditem.date}</small></p>
        <p>{feeditem.description}</p>
        <p><img src="{feeditem.image}" alt="{feeditem.title}"></p>
    <{/feeditem>
</feed>
```

This should give a basic setup to start with.

You can also use the feed in the subject - you have to code it like this:
 
```
{feed url="<<FEEDURL>>"}{feed.title}{/feed}
```

### The following tag must be used in the <feed> block:

##### url
Selects the feed url

### The following tag must be used in the <feeditem> block:

##### loop
Selects the tag which is used to loop through the items.
Value can be empty or "root" to read all items from the root level.

### Example XML

```
<?xml version="1.0" encoding="UTF-8"?>
<root>
	<title>The subject of the e-mail</title>
	<items>
		<item>
			<title>Item #1</title>
			<date>2021-11-16 12:45:01</date>
			<description>The description of item #1</description>
			<image>https://www.example.com/image.png</image>
		</item>
		<item>
			<title>Item #2</title>
			<date>2021-11-17 09:21:43</date>
			<description>The description of item #2</description>
			<image>https://www.example.com/image2.png</image>
		</item>
	</items>
</root>
```