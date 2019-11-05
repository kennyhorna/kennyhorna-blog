<entry>
    <id>{{ $entry->getUrl() }}</id>
    <link type="text/html" rel="alternate" href="{{ $entry->getUrl() }}" />
    <title>{{ $entry->title }}</title>
    <image>
      <title>{{ $entry->title }}</title>
      <url>{{ $entry->cover_image }}</url>
      <link>{{ $entry->getUrl() }}</link>
    </image>
    <published>{{ date(DATE_ATOM, $entry->date) }}</published>
    <updated>{{ date(DATE_ATOM, $entry->date) }}</updated>
    <author>
        <name>{{ $entry->author }}</name>
    </author>
    <summary type="html">{{ $entry->getExcerpt() }}...</summary>
    <content type="html"><![CDATA[
        @include('_posts.' . $entry->getFilename())
    ]]></content>
</entry>
