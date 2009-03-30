<?php

$image_input_url = '{icons}products/webcore_100px.png';
$image_output_url = $this->env->resolve_file($image_input_url);

$this->_munger = new HTML_TEXT_MUNGER ();

// The following tests fails
$this->_run_munger_test (
  "\"buy 20\" rims\"",
  "<p>&ldquo;buy 20\" rims&rdquo;</p>\n"
);

// The following tests fails
$this->_run_munger_test (
  "''cause that's wrong'",
  "<p>&lsquo;&lsquo;cause that&rsquo;s wrong&rsquo;</p>\n"
);

$this->_run_munger_test (
  "'Single-quoted'",
  "<p>&lsquo;Single-quoted&rsquo;</p>\n"
);

$this->_run_munger_test (
  "\"double-quoted\"",
  "<p>&ldquo;double-quoted&rdquo;</p>\n"
);

$this->_run_munger_test (
  "'<a href=\"test.html\">Test</a>'",
  "<p>&lsquo;<a href=\"test.html\">Test</a>&rsquo;</p>\n"
);

$this->_run_munger_test (
  "\"<a href=\"test.html\">Test</a>\"",
  "<p>&ldquo;<a href=\"test.html\">Test</a>&rdquo;</p>\n"
);

$this->_run_munger_test (
  "'<box>Test</box>'",
  "<p>&lsquo;</p>
<div class=\"chart\"><div class=\"chart-body\">Test</div></div><p>&rsquo;</p>\n"
);

$this->_run_munger_test (
  "'<box>Test</box>'",
  "<p>&lsquo;</p>
<div class=\"chart\"><div class=\"chart-body\">Test</div></div><p>&rsquo;</p>\n"
);

$this->_run_munger_test (
  "('Single-quoted')",
  "<p>(&lsquo;Single-quoted&rsquo;)</p>\n"
);

$this->_run_munger_test (
  "(\"double-quoted\")",
  "<p>(&ldquo;double-quoted&rdquo;)</p>\n"
);

$this->_run_munger_test (
  "'(Single-quoted)'",
  "<p>&lsquo;(Single-quoted)&rsquo;</p>\n"
);

$this->_run_munger_test (
  "\"(double-quoted)\"",
  "<p>&ldquo;(double-quoted)&rdquo;</p>\n"
);

$this->_run_munger_test (
  "<'Single-quoted'>",
  "<p>&lt;&lsquo;Single-quoted&rsquo;&gt;</p>\n"
);

$this->_run_munger_test (
  "<\"double-quoted\">",
  "<p>&lt;&ldquo;double-quoted&rdquo;&gt;</p>\n"
);

$this->_run_munger_test (
  "'<Single-quoted>'",
  "<p>&lsquo;&lt;Single-quoted&gt;&rsquo;</p>\n"
);
$this->_run_munger_test (
  "\"<double-quoted>\"",
  "<p>&ldquo;&lt;double-quoted&gt;&rdquo;</p>\n"
);

$this->_run_munger_test (
  "['Single-quoted']",
  "<p>[&lsquo;Single-quoted&rsquo;]</p>\n"
);

$this->_run_munger_test (
  "[\"double-quoted\"]",
  "<p>[&ldquo;double-quoted&rdquo;]</p>\n"
);

$this->_run_munger_test (
  "'[Single-quoted]'",
  "<p>&lsquo;[Single-quoted]&rsquo;</p>\n"
);

$this->_run_munger_test (
  "\"[double-quoted]\"",
  "<p>&ldquo;[double-quoted]&rdquo;</p>\n"
);

$this->_run_munger_test (
  "{'Single-quoted'}",
  "<p>{&lsquo;Single-quoted&rsquo;}</p>\n"
);

$this->_run_munger_test (
  "{\"double-quoted\"}",
  "<p>{&ldquo;double-quoted&rdquo;}</p>\n"
);

$this->_run_munger_test (
  "'{Single-quoted}'",
  "<p>&lsquo;{Single-quoted}&rsquo;</p>\n"
);

$this->_run_munger_test (
  "\"{double-quoted}\"",
  "<p>&ldquo;{double-quoted}&rdquo;</p>\n"
);

$this->_run_munger_test (
  "'Single-quoted.'",
  "<p>&lsquo;Single-quoted.&rsquo;</p>\n"
);

$this->_run_munger_test (
  "\"double-quoted.\"",
  "<p>&ldquo;double-quoted.&rdquo;</p>\n"
);

$this->_run_munger_test (
  "'Single-quoted,'",
  "<p>&lsquo;Single-quoted,&rsquo;</p>\n"
);

$this->_run_munger_test (
  "\"double-quoted,\"",
  "<p>&ldquo;double-quoted,&rdquo;</p>\n"
);

$this->_run_munger_test (
  "'Single-quoted';'Single-quoted';",
  "<p>&lsquo;Single-quoted&rsquo;;&lsquo;Single-quoted&rsquo;;</p>\n"
);

$this->_run_munger_test (
  "'Single-quoted';'Single-quoted';",
  "<p>&lsquo;Single-quoted&rsquo;;&lsquo;Single-quoted&rsquo;;</p>\n"
);

$this->_run_munger_test (
  "'Single-quoted','Single-quoted',",
  "<p>&lsquo;Single-quoted&rsquo;,&lsquo;Single-quoted&rsquo;,</p>\n"
);

$this->_run_munger_test (
  "'Single-quoted'.'Single-quoted'.",
  "<p>&lsquo;Single-quoted&rsquo;.&lsquo;Single-quoted&rsquo;.</p>\n"
);

$this->_run_munger_test (
  "\t'Single-quoted'",
  "<p>\t&lsquo;Single-quoted&rsquo;</p>\n"
);

$this->_run_munger_test (
  "\t\"double-quoted\"",
  "<p>\t&ldquo;double-quoted&rdquo;</p>\n"
);

$this->_run_munger_test (
  "=\"double-quoted\"\r
5'9\"",
  "<p>=ldquo;double-quoted&rdquo;<br>
5'9\"</p>\n"
);

$this->_run_munger_test (
  "$5'000'000'000,00",
  "<p>$5'000'000'000,00</p>\n"
);

$this->_run_munger_test (
  "'500'000'",
  "<p>&lsquo;500'000&rsquo;</p>\n"
);

$this->_run_munger_test (
  "\"500'000\"",
  "<p>&ldquo;500'000&rdquo;</p>\n"
);

$this->_run_munger_test (
  "'don't'",
  "<p>&lsquo;don&rsquo;t&rsquo;</p>\n"
);

$this->_run_munger_test (
  "\"don't\"",
  "<p>&ldquo;don&rsquo;t&rdquo;</p>\n"
);

$this->_run_munger_test (
  "('cause that's wrong)",
  "<p>(&lsquo;cause that&rsquo;s wrong)</p>\n"
);

$this->_run_munger_test (
  "'cause that's wrong",
  "<p>&lsquo;cause that&rsquo;s wrong</p>\n"
);

$this->_run_munger_test (
  "\"'cause that's wrong\"",
  "<p>&ldquo;&lsquo;cause that&rsquo;s wrong&rdquo;</p>\n"
);

$this->_run_munger_test (
  "\"First paragraph

Second paragraph

Third paragraph\"",
  "<p>&ldquo;First paragraph</p>
<p>Second paragraph</p>
<p>Third paragraph&rdquo;</p>\n"
);

$this->_run_munger_test (
  "\"First paragraph

\"Second paragraph

\"Third paragraph\"",
  "<p>&ldquo;First paragraph</p>
<p>&ldquo;Second paragraph</p>
<p>&ldquo;Third paragraph&rdquo;</p>\n"
);

$this->_run_munger_test (
  "buy 20\" rims",
  "<p>buy 20\" rims</p>\n"
);

$this->_run_munger_test (
  "'buy 20\" rims'",
  "<p>&lsquo;buy 20\" rims&rsquo;</p>\n"
);

$this->_run_munger_test (
  "he said \"buy 20!\"; I said \"NO!\"",
  "<p>he said &ldquo;buy 20!&rdquo;; I said &ldquo;NO!&rdquo;</p>\n"
);

$this->_run_munger_test (
  "'Single-quoted', \"double-quoted\",\r
'<a href=\"test.html\">Test</a>', \"<a href=\"test.html\">Test</a>\",\r
'<box>Test</box>', \"<box>Test</box>\"\r
'Single-quoted',\r
\"double-quoted\",\r
\t'Single-quoted',\r
\t\"double-quoted\"\r
('Single-quoted'), (\"double-quoted\")\r
['Single-quoted'], [\"double-quoted\"]\r
{'Single-quoted'}, {\"double-quoted\"}\r
<'Single-quoted'>, <\"double-quoted\">\r
='Single-quoted', =\"double-quoted\"\r
5'9\", $5'000'000'000,00\r
'500'000', \"500'000\"\r
'don't', \"don't\", ('cause that's wrong),\r
(\"'cause that's wrong\"),\r
\"('cause that's wrong)\"\r\n",
  "<p>&lsquo;Single-quoted&rsquo;, &ldquo;double-quoted&rdquo;,<br>
&lsquo;<a href=\"test.html\">Test</a>&rsquo;, &ldquo;<a href=\"test.html\">Test</a>&rdquo;,<br>
&lsquo;</p>
<div class=\"chart\"><div class=\"chart-body\">Test</div></div><p>&rsquo;, &ldquo;</p>
<div class=\"chart\"><div class=\"chart-body\">Test</div></div><p>&rdquo;<br>
&lsquo;Single-quoted&rsquo;,<br>
&ldquo;double-quoted&rdquo;,<br>
\t&lsquo;Single-quoted&rsquo;,<br>
\t&ldquo;double-quoted&rdquo;<br>
(&lsquo;Single-quoted&rsquo;), (&ldquo;double-quoted&rdquo;)<br>
[&lsquo;Single-quoted&rsquo;], [&ldquo;double-quoted&rdquo;]<br>
{&lsquo;Single-quoted&rsquo;}, {&ldquo;double-quoted&rdquo;}<br>
&lt;&lsquo;Single-quoted&rsquo;&gt;, &lt;&ldquo;double-quoted&rdquo;&gt;<br>
=lsquo;Single-quoted&rsquo;, =ldquo;double-quoted&rdquo;<br>
5'9\", $5'000'000'000,00<br>
&lsquo;500'000&rsquo;, &ldquo;500'000&rdquo;<br>
&lsquo;don&rsquo;t&rsquo;, &ldquo;don&rsquo;t&rdquo;, (&lsquo;cause that&rsquo;s wrong),<br>
(&ldquo;&lsquo;cause that&rsquo;s wrong&rdquo;),<br>
&ldquo;(&lsquo;cause that&rsquo;s wrong)&rdquo;<br>
&nbsp;</p>\n"
);

$this->_run_munger_test (
  "--&",
  "<p>&#8211;&amp;</p>\n"
);

$this->_run_munger_test (
  "<div align=\"left\">This is a left-aligned container.</div>",
  "<div style=\"float: left; margin-right: .5em; margin-bottom: .5em\">This is a left-aligned container.</div>"
);

$this->_run_munger_test (
  "<div align=\"right\">This is a right-aligned container.</div>",
  "<div style=\"float: right; margin-left: .5em; margin-bottom: .5em\">This is a right-aligned container.</div>"
);

$this->_run_munger_test (
  "<div align=\"center\">This is a centered container.</div>",
  "<div style=\"margin: auto; display: table\">This is a centered container.</div>"
);

$this->_run_munger_test (
  "<div align=\"center\" width=\"50px\">This is a centered container.</div>",
  "<div style=\"margin: auto; display: table; width: 50px\">This is a centered container.</div>"
);

$this->_run_munger_test (
  "<div>This is a normal container.</div>",
  "<div>This is a normal container.</div>"
);

$this->_run_munger_test (
  "<div width=\"50px\">This is a normal container.</div>",
  "<div style=\"width: 50px\">This is a normal container.</div>"
);


$this->_run_munger_test (
  "<img class=\"frame\" caption=\"Hello World. This is a longer caption for the picture that I'm showing.\" align=\"right\" src=\"$image_input_url\">",
  "<p><span style=\"float: right; margin-left: .5em; margin-bottom: .5em; width: 101px; display: table\"><span class=\"auto-content-inline\"><img src=\"$image_output_url\" alt=\" \" class=\"frame\" style=\"width: 101px\"></span><span class=\"auto-content-caption\">Hello World. This is a longer caption for the picture that I&#039;m showing.</span></span></p>\n"
);

$this->_run_munger_test (
  "<img class=\"frame\" caption=\"Hello World. This is a longer caption for the picture that I'm showing.\" align=\"left\" src=\"$image_input_url\">",
  "<p><span style=\"float: left; margin-right: .5em; margin-bottom: .5em; width: 101px; display: table\"><span class=\"auto-content-inline\"><img src=\"$image_output_url\" alt=\" \" class=\"frame\" style=\"width: 101px\"></span><span class=\"auto-content-caption\">Hello World. This is a longer caption for the picture that I&#039;m showing.</span></span></p>\n"
);

$this->_run_munger_test (
  "<img class=\"frame\" caption=\"Hello World. This is a longer caption for the picture that I'm showing.\" align=\"center\" src=\"$image_input_url\">",
  "<p><span style=\"margin: auto; display: table; width: 101px\"><span class=\"auto-content-inline\"><img src=\"$image_output_url\" alt=\" \" class=\"frame\" style=\"width: 101px\"></span><span class=\"auto-content-caption\">Hello World. This is a longer caption for the picture that I&#039;m showing.</span></span></p>\n"
);

$this->_run_munger_test (
  "<img class=\"frame\" caption=\"Hello World. This is a longer caption for the picture that I'm showing.\" src=\"$image_input_url\">",
  "<p><span style=\"width: 101px; display: table\"><span class=\"auto-content-inline\"><img src=\"$image_output_url\" alt=\" \" class=\"frame\" style=\"width: 101px\"></span><span class=\"auto-content-caption\">Hello World. This is a longer caption for the picture that I&#039;m showing.</span></span></p>\n"
);

$this->_run_munger_test (
  "<img class=\"frame\" caption=\"Hello World. This is a longer caption for the picture that I'm showing.\" attachment=\"10_27_pizi.jpg\">",
  "<p><span><span class=\"auto-content-inline\"><a href=\"10_27_pizi.jpg\"><img src=\"10_27_pizi.jpg\" alt=\" \" class=\"frame\"></a></span><span class=\"auto-content-caption\"><a href=\"10_27_pizi.jpg\">Hello World. This is a longer caption for the picture that I&#039;m showing.</a></span></span></p>\n"
);


$this->_run_munger_test (
  "<bq quote_style=\"none\">First Paragraph
\nSecond Paragraph</bq>",
  "<div class=\"quote quote-block\"><p>First Paragraph</p>
<p>Second Paragraph</p>
</div>"
);

$this->_run_munger_test (
  "<bq quote_style=\"single\">First Paragraph
\nSecond Paragraph</bq>",
  "<div class=\"quote quote-block\"><p>&ldquo;First Paragraph</p>
<p>Second Paragraph&rdquo;</p>
</div>"
);

$this->_run_munger_test (
  "<bq quote_style=\"multiple\">First Paragraph
\nSecond Paragraph</bq>",
  "<div class=\"quote quote-block\"><p>&ldquo;First Paragraph&rdquo;</p>
<p>&ldquo;Second Paragraph&rdquo;</p>
</div>"
);

$this->_run_munger_test (
  "<bq quote_style=\"multiple\">First Paragraph
\nSecond Paragraph
\nThird Paragraph</bq>",
  "<div class=\"quote quote-block\"><p>&ldquo;First Paragraph&rdquo;</p>
<p>&ldquo;Second Paragraph&rdquo;</p>
<p>&ldquo;Third Paragraph&rdquo;</p>
</div>"
);


$this->_run_munger_test (
"Stewart's witty repartee notwithstanding, it is an even greater pleasure to read this extremely well-written and well-founded critique of the same book, \"<a href=\"http://www.amconmag.com/2008/2008_01_28/review.html\" source=\"The American Conservative\">Goldberg’s Trivial Pursuit</a>\"",
"<p>Stewart&rsquo;s witty repartee notwithstanding, it is an even greater pleasure to read this extremely well-written and well-founded critique of the same book, &ldquo;<a href=\"http://www.amconmag.com/2008/2008_01_28/review.html\">Goldberg&#8217;s Trivial Pursuit</a> (<cite><a href=\"http://www.amconmag.com/\">The American Conservative</a></cite>)&rdquo;</p>\n"
);

$this->_run_munger_test (
"Bush is clearly not living in the world we live in. He doesn't lie; it's just that, when he talks, he's not describing the world we live in. In fact, he welcomes us all to join him in his fervent worship of a simplistic black-and-white world where saying something makes it true. A few examples:\r
\r
<ol>\r
Bush is clearly not living in the world we live in. He doesn't lie; it's just that, when he talks, he's not describing the world we live in.\r
<ol> In fact, he welcomes us all to join him in his fervent worship of a simplistic black-and-white world where saying something makes it true. A few examples:\r
ray tell, does that number come from? Does anyone even care anymore when Bush pulls a number out of his ass? How do we know how many people\r
<ul>\r
when he talks, he's not describing the world we live in. In fact, he welcomes Bush is clearly not living in the world we live in. He doesn't lie; it's just that, \r
when he talks, he's not describing the world we live in. In fact, he welcomes \r
<ul>\r
America oozes from it's pores as it strides through the benighted world, bestowing freedom and compassion with a kind paternal hand. Bush touted their recent elections as a\r
</ul>\r
when he talks, he's not describing the world we live in. In fact, he welcomes <ol>\r
America oozes from it's pores as it strides through the benighted world, bestowing freedom and compassion with a kind paternal hand. Bush touted their recent elections as a\r
</ol>\r
</ul>\r
 are in Al Qaeda? How do we even know if it exists, per se? Doesn't matter, we've caught 75% a\r
</ol>\r
Bush is clearly not living in the world we live in. He doesn't lie; it's just that, when he talks, he's not describing the world we live in. In fact, he welcomes us all to join him in his fervent worship of a simplistic black-and-white world where saying something makes it true. A few examples:\r
</ol>\r
\r
<dl dt_class=\"field\">\r
Al Qaeda\r
Apparently, the war on terror is going unbelievably well: it's a catastrophic success. Bush noted in the second debate that <iq>we're bringing Al Qaida to justice. Seventy five percent of them have been brought to justice.</iq> Where, pray tell, does that number come from? Does anyone even care anymore when Bush pulls a number out of his ass? How do we know how many people are in Al Qaeda? How do we even know if it exists, per se? Doesn't matter, we've caught 75% and by the end of his next 4 years, we'll have easily cleaned up the rest. Then we can all celebrate in a world without evil.\r
The Taliban\r
In Bush's fairy-tale world, the Taliban also no longer exist. Because we vanquished them. In the real world, they didn't stay vanquished for long, although they have had to cede power to the warlords throughout most of Afghanistan, where the drug trade has increased by approximately 400 million percent.\r
Afghanistan\r
<div>\r
This is a picture-perfect example of a country that benefitted from the freedom that America oozes from it's pores as it strides through the benighted world, bestowing freedom and compassion with a kind paternal hand. Bush touted their recent elections as an astounding success; <a href=\"http://www.registerguard.com/news/2004/10/19/ed.edit.afghanistan.phn.1019.html\" title=\"Afghanistan's election: U.S., allies must fulfill long-term commitment\">Afghanistan's election</a> gives a brief run-down of the country's situation:\r
\r
<bq>warlords have gained power in ... provinces where their private militias hold sway. The opium trade, once quashed under the Taliban, has been revived on a massive scale. Violence has driven <b>most nonprofit organizations from the countryside</b>. Health experts warn that 70 percent of Afghan people are malnourished; only 13 percent have access to potable water and sanitation. (emphasis added)</bq>\r
\r
Hold on a minute. <i>Doctors without borders</i> has bailed on the country as too dangerous, but their elections are more than just an idle gesture? How can that be? So the people of Afghanistan (those than aren't too starved to even get to a polling station, which would be 30% of them) voted for the president of Kabul (<iq>outside of Kabul, where 80 percent of Afghans live ... Karzai's control is tenuous or nonexistent</iq>)? Wow ... massive progress. A resounding blow for freedom. All signs indicate that the timing of this election once again, suspiciously, benefits Bush, as the article also notes that a lot of work needs to be done to make sure that <iq>this election is not to be Afghanistan's last</iq>. \r
</div>\r
Diplomacy\r
During the second debate, Bush defended his record as a war president, declaring that <iq>obviously we hope that diplomacy works before you ever use force. The hardest decision a president makes is ever to use force.</iq> That statement alone proves there is no God, else he would have been struck dead by 10,000 simultaneous lightning blasts. Later he said that <iq>I tried diplomacy, went to the United Nations.</iq> Just showing up in a building is not diplomacy, jackass. That's called lip service. Just like coming up with new, ever more fantastical reasons to attack Iraq each time one is shot down doesn't count as \"trying to avoid a war\".\r
Weapons of Mass Destruction\r
<div>\r
The recently-issued Duelfer report about WMDs is huge. Almost everyone who reads it or heard his testimony sees overwhelming proof that the sanctions worked and that, after 1995, Saddam had no WMDs left. The fact that, in the last 20 months, no weapons have been found, is also somewhat damning to the Bush administration's justifications for war.\r
\r
Bush to the rescue! He goes the extra mile and notes only that Saddam (who is an evil bastard, we all agree), wasn't playing nice with the UN sanctions. In fact, he was <iq>gaming the oil-for-food program to get rid of sanctions ... [because] ... [h]e wanted to restart his weapons programs</iq>. For Bush, <iq>[t]hat's what the Duelfer report showed. He was deceiving the inspectors.</iq>\r
\r
Period. That's it. There are no other conclusions to draw from the report except that Bush was right all along. Of course, Saddam didn't have any weapons, nor any means to procure them. However, since the sanctions (which neither candidate condemns for the human-rights disaster it was) didn't magically turn Saddam into a nice guy, they obviously weren't enough. See? You see how it was right to go to Iraq? When you look at 7 words out of a 10000 page document and ignore all other physical evidence? You see how a powerful faith can truly move mountains?\r
</div>\r
Election in Iraq\r
These will happen in January and they will mean something. The shining example of Afghanistan will lead Iraq out of the darkness.\r
Coalition of the Willing\r
<div>\r
Look ... the name itself is completely uninspiring. Remember how disgusted Bush was that Kerry <iq>forgot Poland</iq>? Too bad Bush wasted all of that time learning how to pronounce Aleksander Kwasniewski because they have in the meantime also withdrawn their troops. Bush will tell you all day long that the coalition is composed of dozens of strong countries. Kerry nailed him to the wall with these beauties though: <iq>Mr. President, countries are leaving the coalition, not joining. Eight countries have left it.</iq> and this one was just priceless:\r
\r
<bq>If Missouri, just given the number of people from Missouri who are in the military over there today, were a country, it would be the third largest country in the coalition, behind Great Britain and the United States.</bq>\r
\r
Ouch! Naturally, Bush's version of reality survived intact.\r
</div>\r
Missile Defense Shield\r
<div>\r
And finally, a fantasy that many other presidents and candidates also slobber on about. Here's where Bush doesn't even attempt to technically justify the shield -- he just notes that his opponent is opposed to it and smirks. This, for a seemingly impossibly consistent 45% of America, is enough proof that the program is good. Never mind that there is no way it will ever work; never mind that Reagan, one of the century's leading thinkers, hatched this hare-brained idea; never mind that only scientists on a Rebulican think-tank's payroll will offer any comment other than gut-laughing. never mind that it's obvious pandering to his strongest corporate supporters: the military-industrial complex.\r
\r
Kerry, believe it or not, is opposed to it and plans to shut the program down (if possible, I might add ... it might be too much a part of the budget culture by now). There are rumors that work is underway to provide a several-missile deployment in California as \"proof\" that Kim Jong Il is not a threat. Two birds with one subterfuge would be quite a feather in its cap for this administration.\r
</div>\r
</dl>\r
\r
Bush is clearly not living in the world we live in. He doesn't lie; it's just that, when he talks, he's not describing the world we live in. In fact, he welcomes us all to join him in his fervent worship of a simplistic black-and-white world where saying something makes it true. A few examples:\r
Bush is clearly not living in the world we live in. He doesn't lie; it's just that, when he talks, he's not describing the world we live in. In fact, he welcomes us all to join him in his fervent worship of a simplistic black-and-white world where saying something makes it true. A few examples:",
"<p>Bush is clearly not living in the world we live in. He doesn&rsquo;t lie; it&rsquo;s just that, when he talks, he&rsquo;s not describing the world we live in. In fact, he welcomes us all to join him in his fervent worship of a simplistic black-and-white world where saying something makes it true. A few examples:</p>
<ol>
<li><div>Bush is clearly not living in the world we live in. He doesn&rsquo;t lie; it&rsquo;s just that, when he talks, he&rsquo;s not describing the world we live in.<ol>
<li>In fact, he welcomes us all to join him in his fervent worship of a simplistic black-and-white world where saying something makes it true. A few examples:</li>
<li><div>ray tell, does that number come from? Does anyone even care anymore when Bush pulls a number out of his ass? How do we know how many people<ul>
<li>when he talks, he&rsquo;s not describing the world we live in. In fact, he welcomes Bush is clearly not living in the world we live in. He doesn&rsquo;t lie; it&rsquo;s just that, </li>
<li><div>when he talks, he&rsquo;s not describing the world we live in. In fact, he welcomes<ul>
<li>America oozes from it&rsquo;s pores as it strides through the benighted world, bestowing freedom and compassion with a kind paternal hand. Bush touted their recent elections as a</li></ul></div></li>
<li><div>when he talks, he&rsquo;s not describing the world we live in. In fact, he welcomes<ol>
<li>America oozes from it&rsquo;s pores as it strides through the benighted world, bestowing freedom and compassion with a kind paternal hand. Bush touted their recent elections as a</li></ol></div></ul></div></li>
<li>are in Al Qaeda? How do we even know if it exists, per se? Doesn&rsquo;t matter, we&rsquo;ve caught 75% a</li></ol></div></li>
<li>Bush is clearly not living in the world we live in. He doesn&rsquo;t lie; it&rsquo;s just that, when he talks, he&rsquo;s not describing the world we live in. In fact, he welcomes us all to join him in his fervent worship of a simplistic black-and-white world where saying something makes it true. A few examples:</li></ol><dl><dt class=\"field\">Al Qaeda</dt>
<dd>Apparently, the war on terror is going unbelievably well: it&rsquo;s a catastrophic success. Bush noted in the second debate that <span class=\"quote-inline\">&ldquo;we&rsquo;re bringing Al Qaida to justice. Seventy five percent of them have been brought to justice.&rdquo;</span> Where, pray tell, does that number come from? Does anyone even care anymore when Bush pulls a number out of his ass? How do we know how many people are in Al Qaeda? How do we even know if it exists, per se? Doesn&rsquo;t matter, we&rsquo;ve caught 75% and by the end of his next 4 years, we&rsquo;ll have easily cleaned up the rest. Then we can all celebrate in a world without evil.</dd>
<dt class=\"field\">The Taliban</dt>
<dd>In Bush&rsquo;s fairy-tale world, the Taliban also no longer exist. Because we vanquished them. In the real world, they didn&rsquo;t stay vanquished for long, although they have had to cede power to the warlords throughout most of Afghanistan, where the drug trade has increased by approximately 400 million percent.</dd>
<dt class=\"field\">Afghanistan</dt>
<dd><div><p>This is a picture-perfect example of a country that benefitted from the freedom that America oozes from it&rsquo;s pores as it strides through the benighted world, bestowing freedom and compassion with a kind paternal hand. Bush touted their recent elections as an astounding success; <a href=\"http://www.registerguard.com/news/2004/10/19/ed.edit.afghanistan.phn.1019.html\" title=\"Afghanistan&#039;s election: U.S., allies must fulfill long-term commitment\">Afghanistan&rsquo;s election</a> gives a brief run-down of the country&rsquo;s situation:</p>
<div class=\"quote quote-block\">&ldquo;warlords have gained power in &#8230; provinces where their private militias hold sway. The opium trade, once quashed under the Taliban, has been revived on a massive scale. Violence has driven <strong>most nonprofit organizations from the countryside</strong>. Health experts warn that 70 percent of Afghan people are malnourished; only 13 percent have access to potable water and sanitation. (emphasis added)&rdquo;</div><p>Hold on a minute. <em>Doctors without borders</em> has bailed on the country as too dangerous, but their elections are more than just an idle gesture? How can that be? So the people of Afghanistan (those than aren&rsquo;t too starved to even get to a polling station, which would be 30% of them) voted for the president of Kabul (<span class=\"quote-inline\">&ldquo;outside of Kabul, where 80 percent of Afghans live &#8230; Karzai&rsquo;s control is tenuous or nonexistent&rdquo;</span>)? Wow &#8230; massive progress. A resounding blow for freedom. All signs indicate that the timing of this election once again, suspiciously, benefits Bush, as the article also notes that a lot of work needs to be done to make sure that <span class=\"quote-inline\">&ldquo;this election is not to be Afghanistan&rsquo;s last&rdquo;</span>. </p>
</div></dd>
<dt class=\"field\">Diplomacy</dt>
<dd>During the second debate, Bush defended his record as a war president, declaring that <span class=\"quote-inline\">&ldquo;obviously we hope that diplomacy works before you ever use force. The hardest decision a president makes is ever to use force.&rdquo;</span> That statement alone proves there is no God, else he would have been struck dead by 10,000 simultaneous lightning blasts. Later he said that <span class=\"quote-inline\">&ldquo;I tried diplomacy, went to the United Nations.&rdquo;</span> Just showing up in a building is not diplomacy, jackass. That&rsquo;s called lip service. Just like coming up with new, ever more fantastical reasons to attack Iraq each time one is shot down doesn&rsquo;t count as &ldquo;trying to avoid a war&rdquo;.</dd>
<dt class=\"field\">Weapons of Mass Destruction</dt>
<dd><div><p>The recently-issued Duelfer report about WMDs is huge. Almost everyone who reads it or heard his testimony sees overwhelming proof that the sanctions worked and that, after 1995, Saddam had no WMDs left. The fact that, in the last 20 months, no weapons have been found, is also somewhat damning to the Bush administration&rsquo;s justifications for war.</p>
<p>Bush to the rescue! He goes the extra mile and notes only that Saddam (who is an evil bastard, we all agree), wasn&rsquo;t playing nice with the UN sanctions. In fact, he was <span class=\"quote-inline\">&ldquo;gaming the oil-for-food program to get rid of sanctions &#8230; [because] &#8230; [h]e wanted to restart his weapons programs&rdquo;</span>. For Bush, <span class=\"quote-inline\">&ldquo;[t]hat&rsquo;s what the Duelfer report showed. He was deceiving the inspectors.&rdquo;</span></p>
<p>Period. That&rsquo;s it. There are no other conclusions to draw from the report except that Bush was right all along. Of course, Saddam didn&rsquo;t have any weapons, nor any means to procure them. However, since the sanctions (which neither candidate condemns for the human-rights disaster it was) didn&rsquo;t magically turn Saddam into a nice guy, they obviously weren&rsquo;t enough. See? You see how it was right to go to Iraq? When you look at 7 words out of a 10000 page document and ignore all other physical evidence? You see how a powerful faith can truly move mountains?</p>
</div></dd>
<dt class=\"field\">Election in Iraq</dt>
<dd>These will happen in January and they will mean something. The shining example of Afghanistan will lead Iraq out of the darkness.</dd>
<dt class=\"field\">Coalition of the Willing</dt>
<dd><div><p>Look &#8230; the name itself is completely uninspiring. Remember how disgusted Bush was that Kerry <span class=\"quote-inline\">&ldquo;forgot Poland&rdquo;</span>? Too bad Bush wasted all of that time learning how to pronounce Aleksander Kwasniewski because they have in the meantime also withdrawn their troops. Bush will tell you all day long that the coalition is composed of dozens of strong countries. Kerry nailed him to the wall with these beauties though: <span class=\"quote-inline\">&ldquo;Mr. President, countries are leaving the coalition, not joining. Eight countries have left it.&rdquo;</span> and this one was just priceless:</p>
<div class=\"quote quote-block\">&ldquo;If Missouri, just given the number of people from Missouri who are in the military over there today, were a country, it would be the third largest country in the coalition, behind Great Britain and the United States.&rdquo;</div><p>Ouch! Naturally, Bush&rsquo;s version of reality survived intact.</p>
</div></dd>
<dt class=\"field\">Missile Defense Shield</dt>
<dd><div><p>And finally, a fantasy that many other presidents and candidates also slobber on about. Here&rsquo;s where Bush doesn&rsquo;t even attempt to technically justify the shield &#8211; he just notes that his opponent is opposed to it and smirks. This, for a seemingly impossibly consistent 45% of America, is enough proof that the program is good. Never mind that there is no way it will ever work; never mind that Reagan, one of the century&rsquo;s leading thinkers, hatched this hare-brained idea; never mind that only scientists on a Rebulican think-tank&rsquo;s payroll will offer any comment other than gut-laughing. never mind that it&rsquo;s obvious pandering to his strongest corporate supporters: the military-industrial complex.</p>
<p>Kerry, believe it or not, is opposed to it and plans to shut the program down (if possible, I might add &#8230; it might be too much a part of the budget culture by now). There are rumors that work is underway to provide a several-missile deployment in California as &ldquo;proof&rdquo; that Kim Jong Il is not a threat. Two birds with one subterfuge would be quite a feather in its cap for this administration.</p>
</div></dd>
</dl><p>Bush is clearly not living in the world we live in. He doesn&rsquo;t lie; it&rsquo;s just that, when he talks, he&rsquo;s not describing the world we live in. In fact, he welcomes us all to join him in his fervent worship of a simplistic black-and-white world where saying something makes it true. A few examples:<br>
Bush is clearly not living in the world we live in. He doesn&rsquo;t lie; it&rsquo;s just that, when he talks, he&rsquo;s not describing the world we live in. In fact, he welcomes us all to join him in his fervent worship of a simplistic black-and-white world where saying something makes it true. A few examples:</p>\n"
);

$this->_run_munger_test (
"Bush is clearly not living in the world we live in. He doesn't lie; it's just that, when he talks, he's not describing the world we live in. In fact, he welcomes us all to join him in his fervent worship of a simplistic black-and-white world where saying something makes it true. A few examples:\r
\r
<ol>\r
Bush is clearly not living in the world we live in. He doesn't lie; it's just that, when he talks, he's not describing the world we live in.\r
<ol> In fact, he welcomes us all to join him in his fervent worship of a simplistic black-and-white world where saying something makes it true. A few examples:\r
ray tell, does that number come from? Does anyone even care anymore when Bush pulls a number out of his ass? How do we know how many people\r
<ul>\r
when he talks, he's not describing the world we live in. In fact, he welcomes Bush is clearly not living in the world we live in. He doesn't lie; it's just that, \r
when he talks, he's not describing the world we live in. In fact, he welcomes \r
<ul>\r
America oozes from it's pores as it strides through the benighted world, bestowing freedom and compassion with a kind paternal hand. Bush touted their recent elections as a\r
</ul>\r
when he talks, he's not describing the world we live in. In fact, he welcomes <ol>\r
America oozes from it's pores as it strides through the benighted world, bestowing freedom and compassion with a kind paternal hand. Bush touted their recent elections as a\r
</ol>\r
</ul>\r
 are in Al Qaeda? How do we even know if it exists, per se? Doesn't matter, we've caught 75% a\r
</ol>\r
Bush is clearly not living in the world we live in. He doesn't lie; it's just that, when he talks, he's not describing the world we live in. In fact, he welcomes us all to join him in his fervent worship of a simplistic black-and-white world where saying something makes it true. A few examples:\r
</ol>\r
\r
<dl dt_class=\"field\">\r
Al Qaeda\r
Apparently, the war on terror is going unbelievably well: it's a catastrophic success. Bush noted in the second debate that <iq>we're bringing Al Qaida to justice. Seventy five percent of them have been brought to justice.</iq> Where, pray tell, does that number come from? Does anyone even care anymore when Bush pulls a number out of his ass? How do we know how many people are in Al Qaeda? How do we even know if it exists, per se? Doesn't matter, we've caught 75% and by the end of his next 4 years, we'll have easily cleaned up the rest. Then we can all celebrate in a world without evil.\r
The Taliban\r
In Bush's fairy-tale world, the Taliban also no longer exist. Because we vanquished them. In the real world, they didn't stay vanquished for long, although they have had to cede power to the warlords throughout most of Afghanistan, where the drug trade has increased by approximately 400 million percent.\r
Afghanistan\r
<div>\r
This is a picture-perfect example of a country that benefitted from the freedom that America oozes from it's pores as it strides through the benighted world, bestowing freedom and compassion with a kind paternal hand. Bush touted their recent elections as an astounding success; <a href=\"http://www.registerguard.com/news/2004/10/19/ed.edit.afghanistan.phn.1019.html\" title=\"Afghanistan's election: U.S., allies must fulfill long-term commitment\">Afghanistan's election</a> gives a brief run-down of the country's situation:\r
\r
<bq>warlords have gained power in ... provinces where their private militias hold sway. The opium trade, once quashed under the Taliban, has been revived on a massive scale. Violence has driven <b>most nonprofit organizations from the countryside</b>. Health experts warn that 70 percent of Afghan people are malnourished; only 13 percent have access to potable water and sanitation. (emphasis added)</bq>\r
\r
Hold on a minute. <i>Doctors without borders</i> has bailed on the country as too dangerous, but their elections are more than just an idle gesture? How can that be? So the people of Afghanistan (those than aren't too starved to even get to a polling station, which would be 30% of them) voted for the president of Kabul (<iq>outside of Kabul, where 80 percent of Afghans live ... Karzai's control is tenuous or nonexistent</iq>)? Wow ... massive progress. A resounding blow for freedom. All signs indicate that the timing of this election once again, suspiciously, benefits Bush, as the article also notes that a lot of work needs to be done to make sure that <iq>this election is not to be Afghanistan's last</iq>. \r
\r
<ol>\r
Bush is clearly not living in the world we live in. He doesn't lie; it's just that, when he talks, he's not describing the world we live in.\r
<ol> In fact, he welcomes us all to join him in his fervent worship of a simplistic black-and-white world where saying something makes it true. A few examples:\r
ray tell, does that number come from? Does anyone even care anymore when Bush pulls a number out of his ass? How do we know how many people\r
<ul>\r
when he talks, he's not describing the world we live in. In fact, he welcomes Bush is clearly not living in the world we live in. He doesn't lie; it's just that, \r
when he talks, he's not describing the world we live in. In fact, he welcomes \r
<ul>\r
America oozes from it's pores as it strides through the benighted world, bestowing freedom and compassion with a kind paternal hand. Bush touted their recent elections as a\r
</ul>\r
when he talks, he's not describing the world we live in. In fact, he welcomes <ol>\r
America oozes from it's pores as it strides through the benighted world, bestowing freedom and compassion with a kind paternal hand. Bush touted their recent elections as a\r
</ol>\r
</ul>\r
 are in Al Qaeda? How do we even know if it exists, per se? Doesn't matter, we've caught 75% a\r
</ol>\r
Bush is clearly not living in the world we live in. He doesn't lie; it's just that, when he talks, he's not describing the world we live in. In fact, he welcomes us all to join him in his fervent worship of a simplistic black-and-white world where saying something makes it true. A few examples:\r
</ol>\r
\r
</div>\r
Diplomacy\r
During the second debate, Bush defended his record as a war president, declaring that <iq>obviously we hope that diplomacy works before you ever use force. The hardest decision a president makes is ever to use force.</iq> That statement alone proves there is no God, else he would have been struck dead by 10,000 simultaneous lightning blasts. Later he said that <iq>I tried diplomacy, went to the United Nations.</iq> Just showing up in a building is not diplomacy, jackass. That's called lip service. Just like coming up with new, ever more fantastical reasons to attack Iraq each time one is shot down doesn't count as \"trying to avoid a war\".\r
Weapons of Mass Destruction\r
<div>\r
The recently-issued Duelfer report about WMDs is huge. Almost everyone who reads it or heard his testimony sees overwhelming proof that the sanctions worked and that, after 1995, Saddam had no WMDs left. The fact that, in the last 20 months, no weapons have been found, is also somewhat damning to the Bush administration's justifications for war.\r
\r
Bush to the rescue! He goes the extra mile and notes only that Saddam (who is an evil bastard, we all agree), wasn't playing nice with the UN sanctions. In fact, he was <iq>gaming the oil-for-food program to get rid of sanctions ... [because] ... [h]e wanted to restart his weapons programs</iq>. For Bush, <iq>[t]hat's what the Duelfer report showed. He was deceiving the inspectors.</iq>\r
\r
Period. That's it. There are no other conclusions to draw from the report except that Bush was right all along. Of course, Saddam didn't have any weapons, nor any means to procure them. However, since the sanctions (which neither candidate condemns for the human-rights disaster it was) didn't magically turn Saddam into a nice guy, they obviously weren't enough. See? You see how it was right to go to Iraq? When you look at 7 words out of a 10000 page document and ignore all other physical evidence? You see how a powerful faith can truly move mountains?\r
</div>\r
Election in Iraq\r
These will happen in January and they will mean something. The shining example of Afghanistan will lead Iraq out of the darkness.\r
Coalition of the Willing\r
<div>\r
Look ... the name itself is completely uninspiring. Remember how disgusted Bush was that Kerry <iq>forgot Poland</iq>? Too bad Bush wasted all of that time learning how to pronounce Aleksander Kwasniewski because they have in the meantime also withdrawn their troops. Bush will tell you all day long that the coalition is composed of dozens of strong countries. Kerry nailed him to the wall with these beauties though: <iq>Mr. President, countries are leaving the coalition, not joining. Eight countries have left it.</iq> and this one was just priceless:\r
\r
<bq>If Missouri, just given the number of people from Missouri who are in the military over there today, were a country, it would be the third largest country in the coalition, behind Great Britain and the United States.</bq>\r
\r
Ouch! Naturally, Bush's version of reality survived intact.\r
</div>\r
Missile Defense Shield\r
<div>\r
And finally, a fantasy that many other presidents and candidates also slobber on about. Here's where Bush doesn't even attempt to technically justify the shield -- he just notes that his opponent is opposed to it and smirks. This, for a seemingly impossibly consistent 45% of America, is enough proof that the program is good. Never mind that there is no way it will ever work; never mind that Reagan, one of the century's leading thinkers, hatched this hare-brained idea; never mind that only scientists on a Rebulican think-tank's payroll will offer any comment other than gut-laughing. never mind that it's obvious pandering to his strongest corporate supporters: the military-industrial complex.\r
\r
Kerry, believe it or not, is opposed to it and plans to shut the program down (if possible, I might add ... it might be too much a part of the budget culture by now). There are rumors that work is underway to provide a several-missile deployment in California as \"proof\" that Kim Jong Il is not a threat. Two birds with one subterfuge would be quite a feather in its cap for this administration.\r
</div>\r
</dl>\r
\r
Bush is clearly not living in the world we live in. He doesn't lie; it's just that, when he talks, he's not describing the world we live in. In fact, he welcomes us all to join him in his fervent worship of a simplistic black-and-white world where saying something makes it true. A few examples:\r
Bush is clearly not living in the world we live in. He doesn't lie; it's just that, when he talks, he's not describing the world we live in. In fact, he welcomes us all to join him in his fervent worship of a simplistic black-and-white world where saying something makes it true. A few examples:",
"<p>Bush is clearly not living in the world we live in. He doesn&rsquo;t lie; it&rsquo;s just that, when he talks, he&rsquo;s not describing the world we live in. In fact, he welcomes us all to join him in his fervent worship of a simplistic black-and-white world where saying something makes it true. A few examples:</p>
<ol>
<li><div>Bush is clearly not living in the world we live in. He doesn&rsquo;t lie; it&rsquo;s just that, when he talks, he&rsquo;s not describing the world we live in.<ol>
<li>In fact, he welcomes us all to join him in his fervent worship of a simplistic black-and-white world where saying something makes it true. A few examples:</li>
<li><div>ray tell, does that number come from? Does anyone even care anymore when Bush pulls a number out of his ass? How do we know how many people<ul>
<li>when he talks, he&rsquo;s not describing the world we live in. In fact, he welcomes Bush is clearly not living in the world we live in. He doesn&rsquo;t lie; it&rsquo;s just that, </li>
<li><div>when he talks, he&rsquo;s not describing the world we live in. In fact, he welcomes<ul>
<li>America oozes from it&rsquo;s pores as it strides through the benighted world, bestowing freedom and compassion with a kind paternal hand. Bush touted their recent elections as a</li></ul></div></li>
<li><div>when he talks, he&rsquo;s not describing the world we live in. In fact, he welcomes<ol>
<li>America oozes from it&rsquo;s pores as it strides through the benighted world, bestowing freedom and compassion with a kind paternal hand. Bush touted their recent elections as a</li></ol></div></ul></div></li>
<li>are in Al Qaeda? How do we even know if it exists, per se? Doesn&rsquo;t matter, we&rsquo;ve caught 75% a</li></ol></div></li>
<li>Bush is clearly not living in the world we live in. He doesn&rsquo;t lie; it&rsquo;s just that, when he talks, he&rsquo;s not describing the world we live in. In fact, he welcomes us all to join him in his fervent worship of a simplistic black-and-white world where saying something makes it true. A few examples:</li></ol><dl><dt class=\"field\">Al Qaeda</dt>
<dd>Apparently, the war on terror is going unbelievably well: it&rsquo;s a catastrophic success. Bush noted in the second debate that <span class=\"quote-inline\">&ldquo;we&rsquo;re bringing Al Qaida to justice. Seventy five percent of them have been brought to justice.&rdquo;</span> Where, pray tell, does that number come from? Does anyone even care anymore when Bush pulls a number out of his ass? How do we know how many people are in Al Qaeda? How do we even know if it exists, per se? Doesn&rsquo;t matter, we&rsquo;ve caught 75% and by the end of his next 4 years, we&rsquo;ll have easily cleaned up the rest. Then we can all celebrate in a world without evil.</dd>
<dt class=\"field\">The Taliban</dt>
<dd>In Bush&rsquo;s fairy-tale world, the Taliban also no longer exist. Because we vanquished them. In the real world, they didn&rsquo;t stay vanquished for long, although they have had to cede power to the warlords throughout most of Afghanistan, where the drug trade has increased by approximately 400 million percent.</dd>
<dt class=\"field\">Afghanistan</dt>
<dd><div><p>This is a picture-perfect example of a country that benefitted from the freedom that America oozes from it&rsquo;s pores as it strides through the benighted world, bestowing freedom and compassion with a kind paternal hand. Bush touted their recent elections as an astounding success; <a href=\"http://www.registerguard.com/news/2004/10/19/ed.edit.afghanistan.phn.1019.html\" title=\"Afghanistan&#039;s election: U.S., allies must fulfill long-term commitment\">Afghanistan&rsquo;s election</a> gives a brief run-down of the country&rsquo;s situation:</p>
<div class=\"quote quote-block\">&ldquo;warlords have gained power in &#8230; provinces where their private militias hold sway. The opium trade, once quashed under the Taliban, has been revived on a massive scale. Violence has driven <strong>most nonprofit organizations from the countryside</strong>. Health experts warn that 70 percent of Afghan people are malnourished; only 13 percent have access to potable water and sanitation. (emphasis added)&rdquo;</div><p>Hold on a minute. <em>Doctors without borders</em> has bailed on the country as too dangerous, but their elections are more than just an idle gesture? How can that be? So the people of Afghanistan (those than aren&rsquo;t too starved to even get to a polling station, which would be 30% of them) voted for the president of Kabul (<span class=\"quote-inline\">&ldquo;outside of Kabul, where 80 percent of Afghans live &#8230; Karzai&rsquo;s control is tenuous or nonexistent&rdquo;</span>)? Wow &#8230; massive progress. A resounding blow for freedom. All signs indicate that the timing of this election once again, suspiciously, benefits Bush, as the article also notes that a lot of work needs to be done to make sure that <span class=\"quote-inline\">&ldquo;this election is not to be Afghanistan&rsquo;s last&rdquo;</span>. </p>
<ol>
<li><div>Bush is clearly not living in the world we live in. He doesn&rsquo;t lie; it&rsquo;s just that, when he talks, he&rsquo;s not describing the world we live in.<ol>
<li>In fact, he welcomes us all to join him in his fervent worship of a simplistic black-and-white world where saying something makes it true. A few examples:</li>
<li><div>ray tell, does that number come from? Does anyone even care anymore when Bush pulls a number out of his ass? How do we know how many people<ul>
<li>when he talks, he&rsquo;s not describing the world we live in. In fact, he welcomes Bush is clearly not living in the world we live in. He doesn&rsquo;t lie; it&rsquo;s just that, </li>
<li><div>when he talks, he&rsquo;s not describing the world we live in. In fact, he welcomes<ul>
<li>America oozes from it&rsquo;s pores as it strides through the benighted world, bestowing freedom and compassion with a kind paternal hand. Bush touted their recent elections as a</li></ul></div></li>
<li><div>when he talks, he&rsquo;s not describing the world we live in. In fact, he welcomes<ol>
<li>America oozes from it&rsquo;s pores as it strides through the benighted world, bestowing freedom and compassion with a kind paternal hand. Bush touted their recent elections as a</li></ol></div></ul></div></li>
<li>are in Al Qaeda? How do we even know if it exists, per se? Doesn&rsquo;t matter, we&rsquo;ve caught 75% a</li></ol></div></li>
<li>Bush is clearly not living in the world we live in. He doesn&rsquo;t lie; it&rsquo;s just that, when he talks, he&rsquo;s not describing the world we live in. In fact, he welcomes us all to join him in his fervent worship of a simplistic black-and-white world where saying something makes it true. A few examples:</li></ol></div></dd>
<dt class=\"field\">Diplomacy</dt>
<dd>During the second debate, Bush defended his record as a war president, declaring that <span class=\"quote-inline\">&ldquo;obviously we hope that diplomacy works before you ever use force. The hardest decision a president makes is ever to use force.&rdquo;</span> That statement alone proves there is no God, else he would have been struck dead by 10,000 simultaneous lightning blasts. Later he said that <span class=\"quote-inline\">&ldquo;I tried diplomacy, went to the United Nations.&rdquo;</span> Just showing up in a building is not diplomacy, jackass. That&rsquo;s called lip service. Just like coming up with new, ever more fantastical reasons to attack Iraq each time one is shot down doesn&rsquo;t count as &ldquo;trying to avoid a war&rdquo;.</dd>
<dt class=\"field\">Weapons of Mass Destruction</dt>
<dd><div><p>The recently-issued Duelfer report about WMDs is huge. Almost everyone who reads it or heard his testimony sees overwhelming proof that the sanctions worked and that, after 1995, Saddam had no WMDs left. The fact that, in the last 20 months, no weapons have been found, is also somewhat damning to the Bush administration&rsquo;s justifications for war.</p>
<p>Bush to the rescue! He goes the extra mile and notes only that Saddam (who is an evil bastard, we all agree), wasn&rsquo;t playing nice with the UN sanctions. In fact, he was <span class=\"quote-inline\">&ldquo;gaming the oil-for-food program to get rid of sanctions &#8230; [because] &#8230; [h]e wanted to restart his weapons programs&rdquo;</span>. For Bush, <span class=\"quote-inline\">&ldquo;[t]hat&rsquo;s what the Duelfer report showed. He was deceiving the inspectors.&rdquo;</span></p>
<p>Period. That&rsquo;s it. There are no other conclusions to draw from the report except that Bush was right all along. Of course, Saddam didn&rsquo;t have any weapons, nor any means to procure them. However, since the sanctions (which neither candidate condemns for the human-rights disaster it was) didn&rsquo;t magically turn Saddam into a nice guy, they obviously weren&rsquo;t enough. See? You see how it was right to go to Iraq? When you look at 7 words out of a 10000 page document and ignore all other physical evidence? You see how a powerful faith can truly move mountains?</p>
</div></dd>
<dt class=\"field\">Election in Iraq</dt>
<dd>These will happen in January and they will mean something. The shining example of Afghanistan will lead Iraq out of the darkness.</dd>
<dt class=\"field\">Coalition of the Willing</dt>
<dd><div><p>Look &#8230; the name itself is completely uninspiring. Remember how disgusted Bush was that Kerry <span class=\"quote-inline\">&ldquo;forgot Poland&rdquo;</span>? Too bad Bush wasted all of that time learning how to pronounce Aleksander Kwasniewski because they have in the meantime also withdrawn their troops. Bush will tell you all day long that the coalition is composed of dozens of strong countries. Kerry nailed him to the wall with these beauties though: <span class=\"quote-inline\">&ldquo;Mr. President, countries are leaving the coalition, not joining. Eight countries have left it.&rdquo;</span> and this one was just priceless:</p>
<div class=\"quote quote-block\">&ldquo;If Missouri, just given the number of people from Missouri who are in the military over there today, were a country, it would be the third largest country in the coalition, behind Great Britain and the United States.&rdquo;</div><p>Ouch! Naturally, Bush&rsquo;s version of reality survived intact.</p>
</div></dd>
<dt class=\"field\">Missile Defense Shield</dt>
<dd><div><p>And finally, a fantasy that many other presidents and candidates also slobber on about. Here&rsquo;s where Bush doesn&rsquo;t even attempt to technically justify the shield &#8211; he just notes that his opponent is opposed to it and smirks. This, for a seemingly impossibly consistent 45% of America, is enough proof that the program is good. Never mind that there is no way it will ever work; never mind that Reagan, one of the century&rsquo;s leading thinkers, hatched this hare-brained idea; never mind that only scientists on a Rebulican think-tank&rsquo;s payroll will offer any comment other than gut-laughing. never mind that it&rsquo;s obvious pandering to his strongest corporate supporters: the military-industrial complex.</p>
<p>Kerry, believe it or not, is opposed to it and plans to shut the program down (if possible, I might add &#8230; it might be too much a part of the budget culture by now). There are rumors that work is underway to provide a several-missile deployment in California as &ldquo;proof&rdquo; that Kim Jong Il is not a threat. Two birds with one subterfuge would be quite a feather in its cap for this administration.</p>
</div></dd>
</dl><p>Bush is clearly not living in the world we live in. He doesn&rsquo;t lie; it&rsquo;s just that, when he talks, he&rsquo;s not describing the world we live in. In fact, he welcomes us all to join him in his fervent worship of a simplistic black-and-white world where saying something makes it true. A few examples:<br>
Bush is clearly not living in the world we live in. He doesn&rsquo;t lie; it&rsquo;s just that, when he talks, he&rsquo;s not describing the world we live in. In fact, he welcomes us all to join him in his fervent worship of a simplistic black-and-white world where saying something makes it true. A few examples:</p>\n"
);

$this->_run_munger_test (
"This is the last installment of \"stuff I learned during the debates\". Unattributed quotes below are straight from the transcripts, found at <a href=\"http://www.debates.org/pages/trans2004c.html\">Debate #2</a> (\"Rage in the Cage\") and <a href=\"http://www.debates.org/pages/trans2004d.html\">Debate #3</a> (\"Bloodmatch -- back for more Blood\").\r
\r
<h>Head to Head</h>\r
\r
<dl dt_class=\"field\">\r
Patriot Act\r
<ul>\r
Kerry says he supports the Patriot Act. He also says it needs to be pretty much massively rewritten, but he doesn't have the gonads to say it's a piece of crap, because that offends too many of the wrong people.\r
Bush doesn't think the Patriot Act has affected citizen's rights\r
</ul>\r
The Internet(s)\r
<ul>\r
Kerry mentioned his web site several times and directed people there for more information\r
Bush talks about <iq>the Internets</iq>. WTF? Has he ever even used a computer?\r
</ul>\r
Gay marriage\r
<ul>\r
Kerry doesn't support gay marriage, but supports partnerships, whatever that means. We already have a legal definition for when people want to spend their lives together: why do you need to make another one?\r
Bush supports gay marriage if it's <iq>between a man and a woman</iq>.\r
</ul>\r
Ronald Reagan\r
<ul>\r
Kerry loves him. (WTF is up with that?)\r
Bush probably loves him even more\r
</ul>\r
Faith-based programs\r
<ul>\r
Kerry believes in them strongly and will support and increase them\r
Bush invented the damned things and would like to funnel all federal dollars into them, so you need to take a Jesus loyaltly oath to get a piece of bread in a soup kitchen.\r
</ul>\r
Abortion\r
<ul>\r
Kerry spent several minutes talking about abortion without ever (not once) actually saying the word. He seems to probably support a woman's right to choose, but he's so heavily politicized, it's kind of hard to tell.\r
Bush thinks abortion should be outlawed, but will settle for making abortion so difficult, socially sitgmatizing and painful that no one gets them anymore. Rues every dollar of federal money spent on any program that ever uttered the word abortion.\r
</ul>\r
9/11\r
<ul>\r
Kerry thinks this is a reason for <iq>hunting and killing terrorists</iq>\r
Bush thinks this is a reason for everything.\r
</ul>\r
Terrorists\r
<ul>\r
Kerry thinks that terrorists are mindless, soul-eating machines, born to kill without reason and with an undying and unexplainable hatred of America. He thinks we should protect our borders and industrial centers better.\r
Bush thinks that terrorists are mindless, soul-eating machines, born to kill without reason and with an undying and unexplainable hatred of America. He thinks we should <iq>stay on the offensive</iq> ('cause that's worked out great so far)\r
</ul>\r
Changing his mind\r
<ul>\r
Kerry seems to alter his opinions when the facts have changed\r
Bush never, ever, ever reconsiders anything\r
</ul>\r
Health Care\r
<ul>\r
Kerry sees this as the country's greatest failure, pointing out that every other first world country has a system\r
Bush doesn't care how those other pansies are doing it and thinks the current system works just fine\r
</ul>\r
Jobs\r
<ul>\r
Kerry thinks we need to stop tax incentives that encourage American firms from hiring outside of the US. Thinks companies should have to pay a living wage for all jobs and wants to raise the minimum wage. Seems to support prosecuting companies that hire illegals rather than the illegals themselves.\r
Bush thinks that a job is a job, regardless of how much it pays; also thinks that migrant workers should be allowed to do jobs that <iq>no American wants to do</iq>. Is happy with the market regulating pay levels, even if that doesn't result in a living wage. He <iq>will talk about the 1.7 million jobs created since the summer of 2003, and will say that the economy is \"strong and getting stronger.\" That's like boasting about getting a D on your final exam, when you flunked the midterm and needed at least a C to pass the course.</iq> (<a href=\"http://pkarchive.org/column/101204.html\" author=\"Paul Krugman\">Checking the Facts, in Advance</a>)\r
</ul>\r
Social Security\r
<ul>\r
Kerry thinks we should leave it alone and stop thinking up ways to plunder it\r
Bush thinks that young people (teens on up) should be able to invest that money in the stock market instead. They are far more likely to invest it in an iPod or 20-inch rims.\r
</ul>\r
Definition of work\r
<ul>\r
Kerry, several times, in fact, mentioned that he wants programs to have enough money to get their job done\r
Bush is happy to call an increase an improvement, no matter what. (Pell grants here are an example & Bush's administration saw one million more of them distributed, but each grant is, on average only half of what it was four years ago)\r
</ul>\r
The Military\r
<ul>\r
Kerry thinks we're overextended, but also thinks we should have sent more. Thinks we should increase the military by 40,000 people. Perhaps it's jobs program?\r
Bush says there will not be a draft, because everything is hunky dorey.\r
</ul>\r
Religion\r
<ul>\r
Kerry bases his entire life around religion and his faith (<iq>My faith affects everything that I do, in truth. ... I think that everything you do in public life has to be guided by your faith, affected by your faith...</iq>)\r
Bush bases his entire life around religion and his faith (<iq>First, my faith plays a lot -- a big part in my life. And that's, when I was answering that question, what I was really saying to the person was that I pray a lot. And I do.</iq>)\r
</ul>\r
</dl>\r
\r
<h>Kerry Quotes</h>\r
\r
<bq>But you know what we also need to do as Americans is never let the terrorists change the Constitution of the United States in a way that disadvantages our rights.</bq>\r
\r
<bq>Being lectured by the president on fiscal responsibility is a little bit like Tony Soprano talking to me about law and order in this country.</bq>\r
\r
<h>Conclusions</h>\r
\r
Kerry is\r
\r
<ul>\r
a politician, through and through. He stands for something if it doesn't get in the way of his career\r
not going to fix a lot of things that are wrong with America, but has the taken the big step of admitting that there are things wrong with America\r
Talks a lot about war; will stay in Iraq; will hold the course in Israel\r
Also talks a lot about domestic programs and actually addresses problems in a meaningful and halfway-honest way (for a politician)\r
</ul>\r
\r
Bush is\r
\r
<ul>\r
Petulant, whiny, defensive, occasionally hostile; seems to beg you to agree with him\r
Self-contradicting:\r
<ol>\r
He says we can't get cheaper meds from Canada (as he promised four years ago) because they <iq>might not be safe</iq>. When asked what he's going to do about the shortage of flu vaccine, he suggests Canada as our last hope.\r
Does everything possible to protect a baby before it's born; does everything possible to prevent paying for a single thing once it takes its first breath.\r
</ol>\r
Is too stupid to even realize that the <iq>you can run, but you can't hide</iq> slogan backfires horribly because the first guy he applied it to, Osama Bin Laden, is still hiding quite nicely.\r
Seems to be <i>for</i> the Dred Scott decision, which disallowed citizen's rights to any black person, regardless of slave/free status. Is too stupid to realize that he's too stupid to talk about history in any depth or meaningful way.\r
If he's forced to admit that something's <i>not optimal</i>, he claims he <iq>inherited it</iq> from Clinton.\r
</ul>",
"<p>This is the last installment of &ldquo;stuff I learned during the debates&rdquo;. Unattributed quotes below are straight from the transcripts, found at <a href=\"http://www.debates.org/pages/trans2004c.html\">Debate #2</a> (&ldquo;Rage in the Cage&rdquo;) and <a href=\"http://www.debates.org/pages/trans2004d.html\">Debate #3</a> (&ldquo;Bloodmatch &#8211; back for more Blood&rdquo;).</p>
<h3>Head to Head</h3><dl><dt class=\"field\">Patriot Act</dt>
<dd><ul>
<li>Kerry says he supports the Patriot Act. He also says it needs to be pretty much massively rewritten, but he doesn&rsquo;t have the gonads to say it&rsquo;s a piece of crap, because that offends too many of the wrong people.</li>
<li>Bush doesn&rsquo;t think the Patriot Act has affected citizen&rsquo;s rights</li></ul></dd>
<dt class=\"field\">The Internet(s)</dt>
<dd><ul>
<li>Kerry mentioned his web site several times and directed people there for more information</li>
<li>Bush talks about <span class=\"quote-inline\">&ldquo;the Internets&rdquo;</span>. WTF? Has he ever even used a computer?</li></ul></dd>
<dt class=\"field\">Gay marriage</dt>
<dd><ul>
<li>Kerry doesn&rsquo;t support gay marriage, but supports partnerships, whatever that means. We already have a legal definition for when people want to spend their lives together: why do you need to make another one?</li>
<li>Bush supports gay marriage if it&rsquo;s <span class=\"quote-inline\">&ldquo;between a man and a woman&rdquo;</span>.</li></ul></dd>
<dt class=\"field\">Ronald Reagan</dt>
<dd><ul>
<li>Kerry loves him. (WTF is up with that?)</li>
<li>Bush probably loves him even more</li></ul></dd>
<dt class=\"field\">Faith-based programs</dt>
<dd><ul>
<li>Kerry believes in them strongly and will support and increase them</li>
<li>Bush invented the damned things and would like to funnel all federal dollars into them, so you need to take a Jesus loyaltly oath to get a piece of bread in a soup kitchen.</li></ul></dd>
<dt class=\"field\">Abortion</dt>
<dd><ul>
<li>Kerry spent several minutes talking about abortion without ever (not once) actually saying the word. He seems to probably support a woman&rsquo;s right to choose, but he&rsquo;s so heavily politicized, it&rsquo;s kind of hard to tell.</li>
<li>Bush thinks abortion should be outlawed, but will settle for making abortion so difficult, socially sitgmatizing and painful that no one gets them anymore. Rues every dollar of federal money spent on any program that ever uttered the word abortion.</li></ul></dd>
<dt class=\"field\">9/11</dt>
<dd><ul>
<li>Kerry thinks this is a reason for <span class=\"quote-inline\">&ldquo;hunting and killing terrorists&rdquo;</span></li>
<li>Bush thinks this is a reason for everything.</li></ul></dd>
<dt class=\"field\">Terrorists</dt>
<dd><ul>
<li>Kerry thinks that terrorists are mindless, soul-eating machines, born to kill without reason and with an undying and unexplainable hatred of America. He thinks we should protect our borders and industrial centers better.</li>
<li>Bush thinks that terrorists are mindless, soul-eating machines, born to kill without reason and with an undying and unexplainable hatred of America. He thinks we should <span class=\"quote-inline\">&ldquo;stay on the offensive&rdquo;</span> (&lsquo;cause that&rsquo;s worked out great so far)</li></ul></dd>
<dt class=\"field\">Changing his mind</dt>
<dd><ul>
<li>Kerry seems to alter his opinions when the facts have changed</li>
<li>Bush never, ever, ever reconsiders anything</li></ul></dd>
<dt class=\"field\">Health Care</dt>
<dd><ul>
<li>Kerry sees this as the country&rsquo;s greatest failure, pointing out that every other first world country has a system</li>
<li>Bush doesn&rsquo;t care how those other pansies are doing it and thinks the current system works just fine</li></ul></dd>
<dt class=\"field\">Jobs</dt>
<dd><ul>
<li>Kerry thinks we need to stop tax incentives that encourage American firms from hiring outside of the US. Thinks companies should have to pay a living wage for all jobs and wants to raise the minimum wage. Seems to support prosecuting companies that hire illegals rather than the illegals themselves.</li>
<li>Bush thinks that a job is a job, regardless of how much it pays; also thinks that migrant workers should be allowed to do jobs that <span class=\"quote-inline\">&ldquo;no American wants to do&rdquo;</span>. Is happy with the market regulating pay levels, even if that doesn&rsquo;t result in a living wage. He <span class=\"quote-inline\">&ldquo;will talk about the 1.7 million jobs created since the summer of 2003, and will say that the economy is &ldquo;strong and getting stronger.&rdquo; That&rsquo;s like boasting about getting a D on your final exam, when you flunked the midterm and needed at least a C to pass the course.&rdquo;</span> (<a href=\"http://pkarchive.org/column/101204.html\">Checking the Facts, in Advance</a> by <cite>Paul Krugman</cite>)</li></ul></dd>
<dt class=\"field\">Social Security</dt>
<dd><ul>
<li>Kerry thinks we should leave it alone and stop thinking up ways to plunder it</li>
<li>Bush thinks that young people (teens on up) should be able to invest that money in the stock market instead. They are far more likely to invest it in an iPod or 20-inch rims.</li></ul></dd>
<dt class=\"field\">Definition of work</dt>
<dd><ul>
<li>Kerry, several times, in fact, mentioned that he wants programs to have enough money to get their job done</li>
<li>Bush is happy to call an increase an improvement, no matter what. (Pell grants here are an example &amp; Bush&rsquo;s administration saw one million more of them distributed, but each grant is, on average only half of what it was four years ago)</li></ul></dd>
<dt class=\"field\">The Military</dt>
<dd><ul>
<li>Kerry thinks we&rsquo;re overextended, but also thinks we should have sent more. Thinks we should increase the military by 40,000 people. Perhaps it&rsquo;s jobs program?</li>
<li>Bush says there will not be a draft, because everything is hunky dorey.</li></ul></dd>
<dt class=\"field\">Religion</dt>
<dd><ul>
<li>Kerry bases his entire life around religion and his faith (<span class=\"quote-inline\">&ldquo;My faith affects everything that I do, in truth. &#8230; I think that everything you do in public life has to be guided by your faith, affected by your faith&#8230;&rdquo;</span>)</li>
<li>Bush bases his entire life around religion and his faith (<span class=\"quote-inline\">&ldquo;First, my faith plays a lot &#8211; a big part in my life. And that&rsquo;s, when I was answering that question, what I was really saying to the person was that I pray a lot. And I do.&rdquo;</span>)</li></ul></dd>
</dl><h3>Kerry Quotes</h3><div class=\"quote quote-block\">&ldquo;But you know what we also need to do as Americans is never let the terrorists change the Constitution of the United States in a way that disadvantages our rights.&rdquo;</div><div class=\"quote quote-block\">&ldquo;Being lectured by the president on fiscal responsibility is a little bit like Tony Soprano talking to me about law and order in this country.&rdquo;</div><h3>Conclusions</h3><p>Kerry is</p>
<ul>
<li>a politician, through and through. He stands for something if it doesn&rsquo;t get in the way of his career</li>
<li>not going to fix a lot of things that are wrong with America, but has the taken the big step of admitting that there are things wrong with America</li>
<li>Talks a lot about war; will stay in Iraq; will hold the course in Israel</li>
<li>Also talks a lot about domestic programs and actually addresses problems in a meaningful and halfway-honest way (for a politician)</li></ul><p>Bush is</p>
<ul>
<li>Petulant, whiny, defensive, occasionally hostile; seems to beg you to agree with him</li>
<li><div>Self-contradicting:<ol>
<li>He says we can&rsquo;t get cheaper meds from Canada (as he promised four years ago) because they <span class=\"quote-inline\">&ldquo;might not be safe&rdquo;</span>. When asked what he&rsquo;s going to do about the shortage of flu vaccine, he suggests Canada as our last hope.</li>
<li>Does everything possible to protect a baby before it&rsquo;s born; does everything possible to prevent paying for a single thing once it takes its first breath.</li></ol></div></li>
<li>Is too stupid to even realize that the <span class=\"quote-inline\">&ldquo;you can run, but you can&rsquo;t hide&rdquo;</span> slogan backfires horribly because the first guy he applied it to, Osama Bin Laden, is still hiding quite nicely.</li>
<li>Seems to be <em>for</em> the Dred Scott decision, which disallowed citizen&rsquo;s rights to any black person, regardless of slave/free status. Is too stupid to realize that he&rsquo;s too stupid to talk about history in any depth or meaningful way.</li>
<li>If he&rsquo;s forced to admit that something&rsquo;s <em>not optimal</em>, he claims he <span class=\"quote-inline\">&ldquo;inherited it&rdquo;</span> from Clinton.</li></ul>"
);

$this->_run_munger_test (
"I know I've seen this one before, but I got this via email and was kind of struck by some of these numbers (highlighted below).\r
\r
If we could shrink the earth's population to a village of precisely 100 people, with all the existing human ratios remaining the same, it would look something like the following*:\r
\r
<box width=\"75%\" align=\"center\" title=\"Hungry?\">\r
57 Asians\r
21 Europeans\r
<b>14 from the Western Hemisphere, both North and South</b> <span class=\"notes\">(Does this include all of Europe? I think so...)</span>\r
8 Africans\r
52 would be female\r
48 would be male\r
70 would be nonwhite\r
<b>70 would be non-Christian</b> <span class=\"notes\">(a bit of a wake-up call for Bush's base, methinks)</span>\r
89 would be heterosexual\r
11 would be homosexual\r
<b>6 people would possess 59% of the entire world's wealth</b>\r
<b>All 6 would be from the United States</b>\r
80 would live in substandard housing\r
<b>70 would be unable to read</b>\r
50 would suffer from malnutrition\r
1 would be near death;\r
1 would be near birth\r
1 would have a college education\r
<b>1 would own a computer</b> <span class=\"notes\">(a reminder that, relative to the world, you and everone you know is upper class)</span>\r
</box>\r
\r
<span class=\"notes\">*Since the numbers are so massively constrained, rounding errors and the law of averages will cause some interesting numbers to come out. There are rich people living outside of the US -- just not a lot relative to those within the US. I can't verify most of these statistics. If anyone sees some that are out of whack, let me know.</span>",
"<p>I know I&rsquo;ve seen this one before, but I got this via email and was kind of struck by some of these numbers (highlighted below).</p>
<p>If we could shrink the earth&rsquo;s population to a village of precisely 100 people, with all the existing human ratios remaining the same, it would look something like the following*:</p>
<div class=\"chart\" style=\"margin: auto; display: table; width: 75%\"><div class=\"chart-title\">Hungry?</div><div class=\"chart-body\"><p>57 Asians<br>
21 Europeans<br>
<strong>14 from the Western Hemisphere, both North and South</strong> <span class=\"notes\">(Does this include all of Europe? I think so&#8230;)</span><br>
8 Africans<br>
52 would be female<br>
48 would be male<br>
70 would be nonwhite<br>
<strong>70 would be non-Christian</strong> <span class=\"notes\">(a bit of a wake-up call for Bush&rsquo;s base, methinks)</span><br>
89 would be heterosexual<br>
11 would be homosexual<br>
<strong>6 people would possess 59% of the entire world&rsquo;s wealth</strong><br>
<strong>All 6 would be from the United States</strong><br>
80 would live in substandard housing<br>
<strong>70 would be unable to read</strong><br>
50 would suffer from malnutrition<br>
1 would be near death;<br>
1 would be near birth<br>
1 would have a college education<br>
<strong>1 would own a computer</strong> <span class=\"notes\">(a reminder that, relative to the world, you and everone you know is upper class)</span></p>
</div></div><p><span class=\"notes\">*Since the numbers are so massively constrained, rounding errors and the law of averages will cause some interesting numbers to come out. There are rich people living outside of the US &#8211; just not a lot relative to those within the US. I can&rsquo;t verify most of these statistics. If anyone sees some that are out of whack, let me know.</span></p>\n"
);

$this->_run_munger_test (
  "<ol>1.1\r
1.2\r
\r
<box>This is a box</box>\r
1.3\r
1.4\r
<box>This is a box</box>\r
1.5\r
<ol>2.1\r
2.2</ol>\r
1.6\r
1.7\r
<ol>2.1b\r
2.2b</ol>\r
1.8\r
1.9\r
</ol>\r\n",
  "<ol>
<li>1.1</li>
<li>1.2</li>
<li><div class=\"chart\"><div class=\"chart-body\">This is a box</div></div></li>
<li>1.3</li>
<li><div>1.4<div class=\"chart\"><div class=\"chart-body\">This is a box</div></div></div></li>
<li><div>1.5<ol>
<li>2.1</li>
<li>2.2</li></ol></div></li>
<li>1.6</li>
<li><div>1.7<ol>
<li>2.1b</li>
<li>2.2b</li></ol></div></li>
<li>1.8</li>
<li>1.9</li></ol>"
);

$this->_run_munger_test (
  "\r
Testing definition lists.\r
\r
<dl>\r
Definition 1\r
  This is the text of the definition for 1.\r
Definition 2\r
  This is the text of the definition for 2.\r
Definition 3\r
  This is the text of the definition for 3.\r
Definition 4\r
  This is the text of the definition for 4.\r
</dl>\r\n",
  "<p><br>
Testing definition lists.</p>
<dl><dt>Definition 1</dt>
<dd>This is the text of the definition for 1.</dd>
<dt>Definition 2</dt>
<dd>This is the text of the definition for 2.</dd>
<dt>Definition 3</dt>
<dd>This is the text of the definition for 3.</dd>
<dt>Definition 4</dt>
<dd>This is the text of the definition for 4.</dd>
</dl>"
);

$this->_run_munger_test (
  "\r
Testing definition lists.\r
\r
<dl dt_class=\"field\" dd_class=\"notes\">\r
Definition 1\r
  This is the text of the definition for 1.\r
Definition 2\r
  This is the text of the definition for 2.\r
Definition 3\r
  This is the text of the definition for 3.\r
Definition 4\r
  This is the text of the definition for 4.\r
</dl>\r\n",
  "<p><br>
Testing definition lists.</p>
<dl><dt class=\"field\">Definition 1</dt>
<dd class=\"notes\">This is the text of the definition for 1.</dd>
<dt class=\"field\">Definition 2</dt>
<dd class=\"notes\">This is the text of the definition for 2.</dd>
<dt class=\"field\">Definition 3</dt>
<dd class=\"notes\">This is the text of the definition for 3.</dd>
<dt class=\"field\">Definition 4</dt>
<dd class=\"notes\">This is the text of the definition for 4.</dd>
</dl>"
);


$this->_run_munger_test (
"I know I've seen this one before, but I got this via email and was kind of struck by some of these numbers (highlighted below).\r
\r
If we could shrink the earth's population to a village of precisely 100 people, with all the existing human ratios remaining the same, it would look something like the following*:\r
\r
<box width=\"75%\" align=\"center\" title=\"Hungry?\">\r
57 Asians\r
21 Europeans\r
<b>14 from the Western Hemisphere, both North and South</b> <span class=\"notes\">(Does this include all of Europe? I think so...)</span>\r
8 Africans\r
52 would be female\r
48 would be male\r
70 would be nonwhite\r
<b>70 would be non-Christian</b> <span class=\"notes\">(a bit of a wake-up call for Bush's base, methinks)</span>\r
89 would be heterosexual\r
11 would be homosexual\r
<b>6 people would possess 59% of the entire world's wealth</b>\r
<b>All 6 would be from the United States</b>\r
80 would live in substandard housing\r
<b>70 would be unable to read</b>\r
50 would suffer from malnutrition\r
1 would be near death;\r
1 would be near birth\r
1 would have a college education\r
<b>1 would own a computer</b> <span class=\"notes\">(a reminder that, relative to the world, you and everone you know is upper class)</span>\r
</box>\r
\r
<span class=\"notes\">*Since the numbers are so massively constrained, rounding errors and the law of averages will cause some interesting numbers to come out. There are rich people living outside of the US -- just not a lot relative to those within the US. I can't verify most of these statistics. If anyone sees some that are out of whack, let me know.</span>",
"<p>I know I&rsquo;ve seen this one before, but I got this via email and was kind of struck by some of these numbers (highlighted below).</p>
<p>If we could shrink the earth&rsquo;s population to a village of precisely 100 people, with all the existing human ratios remaining the same, it would look something like the following*:</p>
<div class=\"chart\" style=\"margin: auto; display: table; width: 75%\"><div class=\"chart-title\">Hungry?</div><div class=\"chart-body\"><p>57 Asians<br>
21 Europeans<br>
<strong>14 from the Western Hemisphere, both North and South</strong> <span class=\"notes\">(Does this include all of Europe? I think so&#8230;)</span><br>
8 Africans<br>
52 would be female<br>
48 would be male<br>
70 would be nonwhite<br>
<strong>70 would be non-Christian</strong> <span class=\"notes\">(a bit of a wake-up call for Bush&rsquo;s base, methinks)</span><br>
89 would be heterosexual<br>
11 would be homosexual<br>
<strong>6 people would possess 59% of the entire world&rsquo;s wealth</strong><br>
<strong>All 6 would be from the United States</strong><br>
80 would live in substandard housing<br>
<strong>70 would be unable to read</strong><br>
50 would suffer from malnutrition<br>
1 would be near death;<br>
1 would be near birth<br>
1 would have a college education<br>
<strong>1 would own a computer</strong> <span class=\"notes\">(a reminder that, relative to the world, you and everone you know is upper class)</span></p>
</div></div><p><span class=\"notes\">*Since the numbers are so massively constrained, rounding errors and the law of averages will cause some interesting numbers to come out. There are rich people living outside of the US &#8211; just not a lot relative to those within the US. I can&rsquo;t verify most of these statistics. If anyone sees some that are out of whack, let me know.</span></p>\n"
);

$this->_run_munger_test (
  "This<ul>
1.1
<ul>
2.1
2.2
</ul>
1.2
</ul>\n",
  "<p>This</p>
<ul>
<li><div>1.1<ul>
<li>2.1</li>
<li>2.2</li></ul></div></li>
<li>1.2</li></ul>"
);

$this->_run_munger_test (
  "This<ul>
\n1.1
\n<ul>
\n2.1
\n2.2
\n</ul>
\n1.2
\n</ul>
\n",
  "<p>This</p>
<ul>
<li>&nbsp;</li>
<li>1.1</li>
<li><ul>
<li>&nbsp;</li>
<li>2.1</li>
<li>&nbsp;</li>
<li>2.2</li>
<li>&nbsp;</li></ul></li>
<li>&nbsp;</li>
<li>1.2</li>
<li>&nbsp;</li></ul>"
);

$this->_run_munger_test (
  "This<ul>
1.1<ul>
2.1
2.2
</ul>1.2
</ul>\n",
  "<p>This</p>
<ul>
<li><div>1.1<ul>
<li>2.1</li>
<li>2.2</li></ul></div>1.2</li></ul>"
);

$this->_run_munger_test (
  "This<ul>
1.1
<ul>
2.1
<ul>
3.1
<ul>
4.1
4.2
</ul>
3.2
</ul>
2.2
</ul>
1.2
</ul>\n",
  "<p>This</p>
<ul>
<li><div>1.1<ul>
<li><div>2.1<ul>
<li><div>3.1<ul>
<li>4.1</li>
<li>4.2</li></ul></div></li>
<li>3.2</li></ul></div></li>
<li>2.2</li></ul></div></li>
<li>1.2</li></ul>"
);

$this->_run_munger_test (
  "This<ul>
1.1<ul>
2.1<ul>
3.1<ul>
4.1
4.2
</ul>
3.2
</ul>
2.2
</ul>
1.2
</ul>\n",
  "<p>This</p>
<ul>
<li><div>1.1<ul>
<li><div>2.1<ul>
<li><div>3.1<ul>
<li>4.1</li>
<li>4.2</li></ul></div>3.2</li></ul></div>2.2</li></ul></div>1.2</li></ul>"
);

$this->_run_munger_test (
  "This<ul>
1.1<ul>
2.1<ul>
3.1<ul>
\n4.1
\n
\n4.2
</ul>
3.2
</ul>
2.2
</ul>
1.2
</ul>\n",
  "<p>This</p>
<ul>
<li><div>1.1<ul>
<li><div>2.1<ul>
<li><div>3.1<ul>
<li>&nbsp;</li>
<li>4.1</li>
<li>&nbsp;</li>
<li>&nbsp;</li>
<li>&nbsp;</li>
<li>4.2</li></ul></div>3.2</li></ul></div>2.2</li></ul></div>1.2</li></ul>"
);


$this->_run_munger_test (
  "Stuff:\r
<ul>\r
1.1\r
1.2<ul>\r
2.1<ul>\r
3.1\r
3.2\r
3.3\r
\r
</ul>\r
2.2\r
2.3\r
<ul>\r
3.1\r
3.2\r
<ul>\r
4.1\r
</ul>\r
</ul>\r
</ul>\r
Par3\r
Par4\r
Par5\r
</ul>\r\n",
  "<p>Stuff:</p>
<ul>
<li>1.1
<li><div>1.2<ul>
<li><div>2.1<ul>
<li>3.1</li>
<li>3.2</li>
<li>3.3</li>
<li>&nbsp;</li></ul></div>2.2</li><li><div>2.3<ul>
<li>3.1</li>
<li><div>3.2<ul>
<li>4.1</li></ul></div></li></ul></div></li></ul></div>Par3</li>
<li>Par4</li>
<li>Par5</li></ul>"
);

$this->_run_munger_test (
  "Stuff:\r
<ul> Howdy\r
<pre>\r
  pre1.1\r
    pre1.2\r
<ol>\r
pre2.1\r
pre2.2\r
<ol>\r
pre3.1\r
pre3.2\r
</ol>\r
pre2.3\r
</ol>\r
    pre1.3\r
  pre1.4\r
</pre>\r
1.1\r
1.2<ul>\r
2.1<ul>\r
3.1\r
3.2\r
3.3\r
\r
</ul>\r
2.2\r
2.3\r
<ul>\r
3.1\r
3.2\r
<ul>\r
4.1\r
</ul>\r
</ul>\r
</ul>\r
Par3<pre>\r
\tpre2.1\r
\t\tpre2.2\r
\t\tpre2.3\r
\tpre2.4\r
</pre>\r
\r
Par4\r
Par5\r
</ul>\r
\r\n",
  "<p>Stuff:</p>
<ul>
<li><div>Howdy<pre>  pre1.1
    pre1.2
<ol>
<li>pre2.1</li>
<li><div>pre2.2<ol>
<li>pre3.1</li>
<li>pre3.2</li></ol></div></li>
<li>pre2.3</li></ol>
    pre1.3
  pre1.4</pre></div></li>
<li>1.1
<li><div>1.2<ul>
<li><div>2.1<ul>
<li>3.1</li>
<li>3.2</li>
<li>3.3</li>
<li>&nbsp;</li></ul></div>2.2</li><li><div>2.3<ul>
<li>3.1</li>
<li><div>3.2<ul>
<li>4.1</li></ul></div></li></ul></div></li></ul></div><div>Par3<pre>\tpre2.1
\t\tpre2.2
\t\tpre2.3
\tpre2.4</pre></div></li>
<li>Par4</li>
<li>Par5</li></ul>"
);

$this->_run_munger_test (
  "Stuff:\r
<ul> Howdy\r
<pre>\r
\r
  pre1.1\r
    pre1.2\r
<ol>\r
pre2.1\r
pre2.2\r
<ol>\r
pre3.1\r
pre3.2\r
</ol>\r
pre2.3\r
</ol>\r
    pre1.3\r
  pre1.4\r
\r
</pre>\r
1.1\r
1.2<ul>\r
2.1<ul>\r
3.1\r
3.2\r
3.3\r
\r
</ul>\r
2.2\r
2.3\r
<ul>\r
3.1\r
3.2\r
<ul>\r
4.1\r
</ul>\r
</ul>\r
</ul>\r
Par3<pre>\r
\r
\tpre2.1\r
\t\tpre2.2\r
\t\tpre2.3\r
\tpre2.4\r
\r
</pre>\r
\r
Par4\r
Par5\r
</ul>\r
\r\n",
  "<p>Stuff:</p>
<ul>
<li><div>Howdy<pre>
  pre1.1
    pre1.2
<ol>
<li>pre2.1</li>
<li><div>pre2.2<ol>
<li>pre3.1</li>
<li>pre3.2</li></ol></div></li>
<li>pre2.3</li></ol>
    pre1.3
  pre1.4
</pre></div></li>
<li>1.1
<li><div>1.2<ul>
<li><div>2.1<ul>
<li>3.1</li>
<li>3.2</li>
<li>3.3</li>
<li>&nbsp;</li></ul></div>2.2</li><li><div>2.3<ul>
<li>3.1</li>
<li><div>3.2<ul>
<li>4.1</li></ul></div></li></ul></div></li></ul></div><div>Par3<pre>
\tpre2.1
\t\tpre2.2
\t\tpre2.3
\tpre2.4
</pre></div></li>
<li>Par4</li>
<li>Par5</li></ul>"
);


$this->_run_munger_test (
  "<ul>\r
1.1<ul>\r
2.1\r
2.2</ul>\r
1.2<ul>\r
2.1b\r
2.2b</ul>\r
1.3\r
1.4\r
</ul>",
  "<ul>
<li><div>1.1<ul>
<li>2.1</li>
<li>2.2</li></ul></div><div>1.2<ul>
<li>2.1b</li>
<li>2.2b</li></ul></div>1.3</li>
<li>1.4</li></ul>"
);


$this->_run_munger_test (
  "\r
Put your test text here.\r
\r
<ul>\r
  one/one\r
\t<ul>\r
    two/one\r
    two/two\r
    <ul>\r
      three/one\r
    </ul>\r
    two/three\r
  </ul>\r
  one/two\r
  one/three\r
</ul>\r
\r
<ol>\r
  one/one\r
\t<ol>\r
    two/one\r
    two/two\r
    <ol>\r
      three/one\r
    </ol>\r
    two/three\r
  </ol>\r
  one/two\r
  one/three\r
</ol>\r\n",
  "<p><br>
Put your test text here.</p>
<ul>
<li><div>one/one<ul>
<li>two/one</li>
<li><div>two/two<ul>
<li>three/one</li></ul></div></li>
<li>two/three</li></ul></div></li>
<li>one/two</li>
<li>  one/three</li></ul><ol>
<li><div>one/one<ol>
<li>two/one</li>
<li><div>two/two<ol>
<li>three/one</li></ol></div></li>
<li>two/three</li></ol></div></li>
<li>one/two</li>
<li>  one/three</li></ol>"
);

$this->_run_munger_test (
  "\r
Put your test text here.\r
\r
<ul>\r
  one/one\r
\t<ul>\r
    two/one\r
    two/two<ul>\r
      three/one\r
    </ul>\r
    two/three\r
  </ul>\r
  one/two\r
  one/three\r
</ul>\r
\r
<ol>\r
  one/one\r
\t<ol>\r
    two/one\r
    two/two<ol>\r
      three/one\r
    </ol>\r
    two/three\r
  </ol>\r
  one/two\r
  one/three\r
</ol>\r\n",
  "<p><br>
Put your test text here.</p>
<ul>
<li><div>one/one<ul>
<li>two/one
<li><div>two/two<ul>
<li>three/one</li></ul></div>two/three</li></ul></div></li>
<li>one/two</li>
<li>  one/three</li></ul><ol>
<li><div>one/one<ol>
<li>two/one
<li><div>two/two<ol>
<li>three/one</li></ol></div>two/three</li></ol></div></li>
<li>one/two</li>
<li>  one/three</li></ol>"
);


$this->_run_munger_test (
  "\r
Noodles<ul>\r
2 qt water\r
3/4 lb mung bean sprouts\r
6 oz rice noodles (1/4-inch wide)\r
</ul>Sauce<ul>\r
3 tb fresh lime juice\r
3 tb catsup\r
1 tb brown sugar\r
1/4 c  fish sauce* or soy sauce\r
</ul>Remaining Ingredients<ul>\r
3 tb peanut oil or vegetable oil\r
3 to 4 cloves garlic; minced or pressed\r
1 tb fresh chile, minced or 1 1/2 ts crushed red pepper flakes\r
2 c  carrots, grated\r
4 lg eggs, lightly beaten with a pinch of salt\r
2/3 c peanuts, chopped\r
6 to 8 scallions, chopped (about 1 cup)\r
</ul><span class=\"notes\">*Fish sauce is made from fermented salted fish.  It can be found in Asian food stores and requires no refrigeration after opening.</span>\r\n",
  "<p><br>
Noodles</p>
<ul>
<li>2 qt water</li>
<li>&frac34; lb mung bean sprouts</li>
<li>6 oz rice noodles (&frac14;-inch wide)</li></ul><p>Sauce</p>
<ul>
<li>3 tb fresh lime juice</li>
<li>3 tb catsup</li>
<li>1 tb brown sugar</li>
<li>&frac14; c  fish sauce* or soy sauce</li></ul><p>Remaining Ingredients</p>
<ul>
<li>3 tb peanut oil or vegetable oil</li>
<li>3 to 4 cloves garlic; minced or pressed</li>
<li>1 tb fresh chile, minced or 1 &frac12; ts crushed red pepper flakes</li>
<li>2 c  carrots, grated</li>
<li>4 lg eggs, lightly beaten with a pinch of salt</li>
<li>2/3 c peanuts, chopped</li>
<li>6 to 8 scallions, chopped (about 1 cup)</li></ul><p><span class=\"notes\">*Fish sauce is made from fermented salted fish.  It can be found in Asian food stores and requires no refrigeration after opening.</span><br>
&nbsp;</p>\n"
);

$this->_run_munger_test (
  "\r
Noodles\r
<ul>\r
2 qt water\r
3/4 lb mung bean sprouts\r
6 oz rice noodles (1/4-inch wide)\r
</ul>\r
Sauce\r
<ul>\r
3 tb fresh lime juice\r
3 tb catsup\r
1 tb brown sugar\r
1/4 c  fish sauce* or soy sauce\r
</ul>\r
Remaining Ingredients\r
<ul>\r
3 tb peanut oil or vegetable oil\r
3 to 4 cloves garlic; minced or pressed\r
1 tb fresh chile, minced or 1 1/2 ts crushed red pepper flakes\r
2 c  carrots, grated\r
4 lg eggs, lightly beaten with a pinch of salt\r
2/3 c peanuts, chopped\r
6 to 8 scallions, chopped (about 1 cup)\r
</ul>\r
<span class=\"notes\">*Fish sauce is made from fermented salted fish.  It can be found in Asian food stores and requires no refrigeration after opening.</span>\r\n",
  "<p><br>
Noodles</p>
<ul>
<li>2 qt water</li>
<li>&frac34; lb mung bean sprouts</li>
<li>6 oz rice noodles (&frac14;-inch wide)</li></ul><p>Sauce</p>
<ul>
<li>3 tb fresh lime juice</li>
<li>3 tb catsup</li>
<li>1 tb brown sugar</li>
<li>&frac14; c  fish sauce* or soy sauce</li></ul><p>Remaining Ingredients</p>
<ul>
<li>3 tb peanut oil or vegetable oil</li>
<li>3 to 4 cloves garlic; minced or pressed</li>
<li>1 tb fresh chile, minced or 1 &frac12; ts crushed red pepper flakes</li>
<li>2 c  carrots, grated</li>
<li>4 lg eggs, lightly beaten with a pinch of salt</li>
<li>2/3 c peanuts, chopped</li>
<li>6 to 8 scallions, chopped (about 1 cup)</li></ul><p><span class=\"notes\">*Fish sauce is made from fermented salted fish.  It can be found in Asian food stores and requires no refrigeration after opening.</span><br>
&nbsp;</p>\n"
);

$this->_run_munger_test (
  "\r
Noodles\r
<ul>\r
2 qt water\r
3/4 lb mung bean sprouts\r
6 oz rice noodles (1/4-inch wide)\r
</ul>\r
\r
Sauce\r
\r
<ul>\r
3 tb fresh lime juice\r
3 tb catsup\r
1 tb brown sugar\r
1/4 c  fish sauce* or soy sauce\r
</ul>\r
\r
Remaining Ingredients\r
\r
<ul>\r
3 tb peanut oil or vegetable oil\r
3 to 4 cloves garlic; minced or pressed\r
1 tb fresh chile, minced or 1 1/2 ts crushed red pepper flakes\r
2 c  carrots, grated\r
4 lg eggs, lightly beaten with a pinch of salt\r
2/3 c peanuts, chopped\r
6 to 8 scallions, chopped (about 1 cup)\r
</ul>\r
\r
<span class=\"notes\">*Fish sauce is made from fermented salted fish.  It can be found in Asian food stores and requires no refrigeration after opening.</span>\r\n",
  "<p><br>
Noodles</p>
<ul>
<li>2 qt water</li>
<li>&frac34; lb mung bean sprouts</li>
<li>6 oz rice noodles (&frac14;-inch wide)</li></ul><p>Sauce</p>
<ul>
<li>3 tb fresh lime juice</li>
<li>3 tb catsup</li>
<li>1 tb brown sugar</li>
<li>&frac14; c  fish sauce* or soy sauce</li></ul><p>Remaining Ingredients</p>
<ul>
<li>3 tb peanut oil or vegetable oil</li>
<li>3 to 4 cloves garlic; minced or pressed</li>
<li>1 tb fresh chile, minced or 1 &frac12; ts crushed red pepper flakes</li>
<li>2 c  carrots, grated</li>
<li>4 lg eggs, lightly beaten with a pinch of salt</li>
<li>2/3 c peanuts, chopped</li>
<li>6 to 8 scallions, chopped (about 1 cup)</li></ul><p><span class=\"notes\">*Fish sauce is made from fermented salted fish.  It can be found in Asian food stores and requires no refrigeration after opening.</span><br>
&nbsp;</p>\n"
);

$this->_run_munger_test (
  "\r
\r
Noodles\r
\r
\r
<ul>\r
2 qt water\r
3/4 lb mung bean sprouts\r
6 oz rice noodles (1/4-inch wide)\r
</ul>\r
\r
\r
Sauce\r
\r
\r
<ul>\r
3 tb fresh lime juice\r
3 tb catsup\r
1 tb brown sugar\r
1/4 c  fish sauce* or soy sauce\r
</ul>\r
\r
\r
Remaining Ingredients\r
\r
\r
<ul>\r
3 tb peanut oil or vegetable oil\r
3 to 4 cloves garlic; minced or pressed\r
1 tb fresh chile, minced or 1 1/2 ts crushed red pepper flakes\r
2 c  carrots, grated\r
4 lg eggs, lightly beaten with a pinch of salt\r
2/3 c peanuts, chopped\r
6 to 8 scallions, chopped (about 1 cup)\r
</ul>\r
\r
\r
<span class=\"notes\">*Fish sauce is made from fermented salted fish.  It can be found in Asian food stores and requires no refrigeration after opening.</span>\r\n",
  "<p>&nbsp;</p>
<p>Noodles<br>
&nbsp;</p>
<ul>
<li>2 qt water</li>
<li>&frac34; lb mung bean sprouts</li>
<li>6 oz rice noodles (&frac14;-inch wide)</li></ul><p><br>
Sauce<br>
&nbsp;</p>
<ul>
<li>3 tb fresh lime juice</li>
<li>3 tb catsup</li>
<li>1 tb brown sugar</li>
<li>&frac14; c  fish sauce* or soy sauce</li></ul><p><br>
Remaining Ingredients<br>
&nbsp;</p>
<ul>
<li>3 tb peanut oil or vegetable oil</li>
<li>3 to 4 cloves garlic; minced or pressed</li>
<li>1 tb fresh chile, minced or 1 &frac12; ts crushed red pepper flakes</li>
<li>2 c  carrots, grated</li>
<li>4 lg eggs, lightly beaten with a pinch of salt</li>
<li>2/3 c peanuts, chopped</li>
<li>6 to 8 scallions, chopped (about 1 cup)</li></ul><p><br>
<span class=\"notes\">*Fish sauce is made from fermented salted fish.  It can be found in Asian food stores and requires no refrigeration after opening.</span><br>
&nbsp;</p>\n"
);


$this->_run_munger_test (
"\r
democratically by getting anti-war sentiments labelled as treason. Their recommendation is to send along two witnesses for each protester in order to protect the ability to prosecute peace-protesters for treason in the future. Once the laws are appropriately amended.\r
\r
<box width=\"25%\" align=\"right\" class=\"excerpt\">\r
This is the quote:\r
\r
<bq>I believe we are one more 9/11 away from the end of the open society.\r
\r
I really do.\r
</bq>\r
\r
<div class=\"notes\" style=\"text-align: right\">- Thomas Friedman</div>\r
\r
Fertig.\r
</box>\r
I'm not kidding, nor am I misinterpreting. This is the state of your news today. They are equating being against Bush's war with treason. Not agreeing with the govern",
"<p><br>
democratically by getting anti-war sentiments labelled as treason. Their recommendation is to send along two witnesses for each protester in order to protect the ability to prosecute peace-protesters for treason in the future. Once the laws are appropriately amended.</p>
<div class=\"chart\" style=\"float: right; margin-left: .5em; margin-bottom: .5em; width: 25%\"><div class=\"chart-body excerpt\"><p>This is the quote:</p>
<div class=\"quote quote-block\"><p>&ldquo;I believe we are one more 9/11 away from the end of the open society.</p>
<p>&ldquo;I really do.<br>
&rdquo;</p>
</div><div class=\"notes\" style=\"text-align: right\">- Thomas Friedman</div><p>Fertig.</p>
</div></div><p>I&rsquo;m not kidding, nor am I misinterpreting. This is the state of your news today. They are equating being against Bush&rsquo;s war with treason. Not agreeing with the govern</p>\n"
);

$this->_munger->force_paragraphs = false;

$this->_run_munger_test (
  "< <<Whatever this is, eh?>",
  "&lt; &lt;Whatever this is, eh?&gt;"
);

$this->_run_munger_test ('<<<<<', '&lt;&lt;&lt;');
$this->_run_munger_test ('<<f<<s', '&lt;f&lt;s');
$this->_run_munger_test ('<<<<<<f<<s<<G<<4<<<e><', '&lt;&lt;&lt;f&lt;s&lt;G&lt;4&lt;&lt;e&gt;&lt;');
$this->_run_munger_test ('<<<f><<s', '&lt;&lt;f&gt;&lt;s');

$this->_munger->highlighted_words = 'name here';

$this->_run_munger_test (
"\r
Testing headings.\r
\r
\r
Testing headings.\r
\r
<h level=\"1\">H1 heading</h>\r
\r
Here's some text under this heading\r
\r
<h>Normal title</h>\r
\r
Here's some text under this heading (level 3).\r
\r
<h level=\"high\">Bogus heading</h>\r
\r
\r
\r
\r
<h>Multi-line\r
heading</h>\r
\r\n",
"<p><br>
Testing headings.</p>
<p><br>
Testing headings.</p>
<h1>H1 heading</h1><p><span class=\"highlight\">Here</span>&rsquo;s some text under this heading</p>
<h3>Normal title</h3><p><span class=\"highlight\">Here</span>&rsquo;s some text under this heading (level 3).</p>
<h3>Bogus heading</h3><p><br>
&nbsp;</p>
<h3>Multi-line
heading</h3>"
);

$this->_munger->max_visible_output_chars = 0;
$this->_run_munger_test (
  "<a href=\"/earthli/index.php\">home</a> <p>This is the home page.</p> Hello. I think 8 > 5 && 5 < 8.",
  "<a href=\"/earthli/index.php\">home</a> &lt;p&gt;This is the home page.&lt;/p&gt; Hello. I think 8 &gt; 5 &amp;&amp; 5 &lt; 8."
);


$this->_munger->max_visible_output_chars = 6;
$this->_munger->force_paragraphs = true;
$this->_run_munger_test (
  "This is my name.",
  "<p>This...</p>\n"
);


$this->_munger->max_visible_output_chars = 0;

$this->_munger->force_paragraphs = false;

$this->_run_munger_test (
  "Horace Greeley",
  "Horace Greeley"
);

$this->_run_munger_test (
  "Line
break
\nParagraph
\n
Par/Line Break
\n
\nTwo Paragraphs
\n
\n
Two Paragraphs/Line Break
\n
\n
\nThree Paragraphs",
  "<p>Line<br>
break</p>
<p>Paragraph</p>
<p><br>
Par/Line Break</p>
<p>&nbsp;</p>
<p>Two Paragraphs</p>
<p>&nbsp;</p>
<p><br>
Two Paragraphs/Line Break</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>Three Paragraphs</p>\n"
);


$this->_run_munger_test (
  "Horace Greeley <img src=\"$image_input_url\">Hello",
  "Horace Greeley <img src=\"$image_output_url\" alt=\" \">Hello"
);

$this->_run_munger_test (
  "Horace Greeley <p>This is (not) a paragraph</p>",
  "Horace Greeley &lt;p&gt;This is (not) a paragraph&lt;/p&gt;"
);

$this->_run_munger_test (
  "Horace Greeley likes to write in German (üöäÖ)",
  "Horace Greeley likes to write in German (&uuml;&ouml;&auml;&Ouml;)"
);

$this->_run_munger_test (
  "This is a code sample: 
<pre>function DoSomething( _op: TOperation );
\tbegin
\t\texit;
\tend;
</pre>That was a code sample.",
  "<p>This is a code sample: </p>
<pre>function DoSomething( _op: TOperation );
\tbegin
\t\texit;
\tend;</pre><p>That was a code sample.</p>\n"
);

$this->_run_munger_test (
  "This is a code sample in a box: 
<box title=\"Code Sample\"><pre>function DoSomething( _op: TOperation );
\tbegin
\t\texit;
\tend;
</pre></box>That was a code sample.",
  "<p>This is a code sample in a box: </p>
<div class=\"chart\"><div class=\"chart-title\">Code Sample</div><div class=\"chart-body\"><pre>function DoSomething( _op: TOperation );
\tbegin
\t\texit;
\tend;</pre></div></div><p>That was a code sample.</p>\n"
);

$this->_run_munger_test (
  "This is a code sample in a box: 
<box align=\"right\" title=\"Code Sample\"><pre>function DoSomething( _op: TOperation );
\tbegin
\t\texit;
\tend;
</pre></box>That was a code sample.",
  "<p>This is a code sample in a box: </p>
<div class=\"chart\" style=\"float: right; margin-left: .5em; margin-bottom: .5em\"><div class=\"chart-title\">Code Sample</div><div class=\"chart-body\"><pre>function DoSomething( _op: TOperation );
\tbegin
\t\texit;
\tend;</pre></div></div><p>That was a code sample.</p>\n"
);

$this->_run_munger_test (
  "This is a code sample in a box: 
<box align=\"left\" title=\"Code Sample\"><pre>function DoSomething( _op: TOperation );
\tbegin
\t\texit;
\tend;
</pre></box>That was a code sample.",
  "<p>This is a code sample in a box: </p>
<div class=\"chart\" style=\"float: left; margin-right: .5em; margin-bottom: .5em\"><div class=\"chart-title\">Code Sample</div><div class=\"chart-body\"><pre>function DoSomething( _op: TOperation );
\tbegin
\t\texit;
\tend;</pre></div></div><p>That was a code sample.</p>\n"
);

$this->_run_munger_test (
  "This is a code sample in a box: 
<box align=\"center\" title=\"Code Sample\"><pre>function DoSomething( _op: TOperation );
\tbegin
\t\texit;
\tend;
</pre></box>That was a code sample.",
  "<p>This is a code sample in a box: </p>
<div class=\"chart\" style=\"margin: auto; display: table\"><div class=\"chart-title\">Code Sample</div><div class=\"chart-body\"><pre>function DoSomething( _op: TOperation );
\tbegin
\t\texit;
\tend;</pre></div></div><p>That was a code sample.</p>\n"
);

$this->_run_munger_test (
  "This is a code sample in a box: 
<box align=\"right\" width=\"50%\" title=\"Code Sample\"><pre>function DoSomething( _op: TOperation );
\tbegin
\t\texit;
\tend;
</pre></box>That was a code sample.",
  "<p>This is a code sample in a box: </p>
<div class=\"chart\" style=\"float: right; margin-left: .5em; margin-bottom: .5em; width: 50%\"><div class=\"chart-title\">Code Sample</div><div class=\"chart-body\"><pre>function DoSomething( _op: TOperation );
\tbegin
\t\texit;
\tend;</pre></div></div><p>That was a code sample.</p>\n"
);

$this->_run_munger_test (
  "This is a code sample: 
<pre>function DoSomething( _op: TOperation );
\tbegin
\t\texit;
\tend;
</pre>
That was a code sample.",
  "<p>This is a code sample: </p>
<pre>function DoSomething( _op: TOperation );
\tbegin
\t\texit;
\tend;</pre><p>That was a code sample.</p>\n"
);


$this->_run_munger_test (
  "This<ul><a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a></ul> here.",
  "<p>This</p>
<ul>
<li><a href=\"something.php\">is</a> my</li>
<li>first <a href=\"something.php\"><span class=\"highlight\">name</span></a></li></ul><p> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_run_munger_test (
  "This<ul>
\n<a href=\"something.php\">is</a> my
first
 <a href=\"something.php\">name</a>
</ul> here.",
  "<p>This</p>
<ul>
<li>&nbsp;</li>
<li><a href=\"something.php\">is</a> my</li>
<li>first</li>
<li> <a href=\"something.php\"><span class=\"highlight\">name</span></a></li></ul><p> <span class=\"highlight\">here</span>.</p>\n"
);


$this->_run_munger_test (
  "This<ul>
\n<a href=\"something.php\">is</a> my
<ul>first
 <a href=\"something.php\">name</a>
</ul> here.
</ul>",
  "<p>This</p>
<ul>
<li>&nbsp;</li>
<li><div><a href=\"something.php\">is</a> my<ul>
<li>first</li>
<li> <a href=\"something.php\"><span class=\"highlight\">name</span></a></li></ul></div></li>
<li><span class=\"highlight\">here</span>.</li></ul>"
);

$this->_run_munger_test (
  "This<ul>
\n<a href=\"something.php\">is</a> my
<ul>first
 <a href=\"something.php\">name</a>
</ul>
 here.
</ul>",
  "<p>This</p>
<ul>
<li>&nbsp;</li>
<li><div><a href=\"something.php\">is</a> my<ul>
<li>first</li>
<li> <a href=\"something.php\"><span class=\"highlight\">name</span></a></li></ul></div></li>
<li><span class=\"highlight\">here</span>.</li></ul>"
);

$this->_run_munger_test (
  "This<ul>
\n<a href=\"something.php\">is</a> my<ul>first
 <a href=\"something.php\">name</a>
</ul>
 here.
</ul>",
  "<p>This</p>
<ul>
<li>
<li><div><a href=\"something.php\">is</a> my<ul>
<li>first</li>
<li> <a href=\"something.php\"><span class=\"highlight\">name</span></a></li></ul></div><span class=\"highlight\">here</span>.</li></ul>"
);


$this->_run_munger_test (
  "This<ul>
<a href=\"something.php\">is</a> my
first
 <a href=\"something.php\">name</a>
</ul> here.",
  "<p>This</p>
<ul>
<li><a href=\"something.php\">is</a> my</li>
<li>first</li>
<li> <a href=\"something.php\"><span class=\"highlight\">name</span></a></li></ul><p> <span class=\"highlight\">here</span>.</p>\n"
);


$this->_run_munger_test (
  "This<ul>
<a href=\"something.php\">is</a> my
<ul>first
 <a href=\"something.php\">name</a>
</ul> here.
</ul>",
  "<p>This</p>
<ul>
<li><div><a href=\"something.php\">is</a> my<ul>
<li>first</li>
<li> <a href=\"something.php\"><span class=\"highlight\">name</span></a></li></ul></div></li>
<li><span class=\"highlight\">here</span>.</li></ul>"
);

$this->_run_munger_test (
  "This<ul>
<a href=\"something.php\">is</a> my
<ul>first
 <a href=\"something.php\">name</a>
</ul>
 here.
</ul>",
  "<p>This</p>
<ul>
<li><div><a href=\"something.php\">is</a> my<ul>
<li>first</li>
<li> <a href=\"something.php\"><span class=\"highlight\">name</span></a></li></ul></div></li>
<li><span class=\"highlight\">here</span>.</li></ul>"
);


$this->_run_munger_test (
  "<ol>\r
num1.1\r
num1.2\r
<ol>\r
num2.1\r
num2.2\r
</ol>\r
num1.3\r
</ol>",
  "<ol>
<li>num1.1</li>
<li><div>num1.2<ol>
<li>num2.1</li>
<li>num2.2</li></ol></div></li>
<li>num1.3</li></ol>"
);

$this->_run_munger_test (
  "<ol>\r
num1.1\r
num1.2\r
num1.3\r
num1.4\r
<ol>\r
num2.1\r
num2.2\r
<ol>\r
num3.1\r
\r
\r
num3.4\r
<ol>\r
num4.1\r
num4.2\r
</ol>\r
</ol>\r
\r
num2.4\r
\r
\r
</ol>\r
num1.5\r
</ol>",
  "<ol>
<li>num1.1</li>
<li>num1.2</li>
<li>num1.3</li>
<li><div>num1.4<ol>
<li>num2.1</li>
<li><div>num2.2<ol>
<li>num3.1</li>
<li>&nbsp;</li>
<li>&nbsp;</li>
<li><div>num3.4<ol>
<li>num4.1</li>
<li>num4.2</li></ol></div></li></ol></div></li>
<li>&nbsp;</li>
<li>num2.4</li>
<li>&nbsp;</li>
<li>&nbsp;</li></ol></div></li>
<li>num1.5</li></ol>"
);


$this->_munger->force_paragraphs = false;
$this->_munger->break_inside_word = false;

$this->_munger->max_visible_output_chars = 0;
$this->_run_munger_test (
  "This is my name.",
  "This is my <span class=\"highlight\">name</span>."
);

$this->_munger->max_visible_output_chars = 1;
$this->_run_munger_test (
  "This is my name.",
  "This..."
);

$this->_munger->max_visible_output_chars = 2;
$this->_run_munger_test (
  "This is my name.",
  "This..."
);

$this->_munger->max_visible_output_chars = 3;
$this->_run_munger_test (
  "This is my name.",
  "This..."
);

$this->_munger->max_visible_output_chars = 4;
$this->_run_munger_test (
  "This is my name.",
  "This..."
);

$this->_munger->max_visible_output_chars = 5;
$this->_run_munger_test (
  "This is my name.",
  "This..."
);

$this->_munger->max_visible_output_chars = 6;
$this->_run_munger_test (
  "This is my name.",
  "This..."
);

$this->_munger->max_visible_output_chars = 7;
$this->_run_munger_test (
  "This is my name.",
  "This..."
);

$this->_munger->max_visible_output_chars = 8;
$this->_run_munger_test (
  "This is my name.",
  "This is..."
);

$this->_munger->max_visible_output_chars = 9;
$this->_run_munger_test (
  "This is my name.",
  "This is..."
);

$this->_munger->max_visible_output_chars = 10;
$this->_run_munger_test (
  "This is my name.",
  "This is..."
);

$this->_munger->max_visible_output_chars = 11;
$this->_run_munger_test (
  "This is my name.",
  "This is my..."
);

$this->_munger->max_visible_output_chars = 12;
$this->_run_munger_test (
  "This is my name.",
  "This is my..."
);

$this->_munger->max_visible_output_chars = 13;
$this->_run_munger_test (
  "This is my name.",
  "This is my..."
);

$this->_munger->max_visible_output_chars = 14;
$this->_run_munger_test (
  "This is my name.",
  "This is my..."
);

$this->_munger->max_visible_output_chars = 15;
$this->_run_munger_test (
  "This is my name.",
  "This is my..."
);

$this->_munger->max_visible_output_chars = 16;
$this->_run_munger_test (
  "This is my name.",
  "This is my <span class=\"highlight\">name</span>."
);

$this->_munger->max_visible_output_chars = 17;
$this->_run_munger_test (
  "This is my name.",
  "This is my <span class=\"highlight\">name</span>."
);

$this->_munger->max_visible_output_chars = 18;
$this->_run_munger_test (
  "This is my name.",
  "This is my <span class=\"highlight\">name</span>."
);

$this->_munger->max_visible_output_chars = 19;
$this->_run_munger_test (
  "This is my name.",
  "This is my <span class=\"highlight\">name</span>."
);


$this->_munger->max_visible_output_chars = 0;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 1;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This..."
);

$this->_munger->max_visible_output_chars = 2;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This..."
);

$this->_munger->max_visible_output_chars = 3;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This..."
);

$this->_munger->max_visible_output_chars = 4;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This..."
);

$this->_munger->max_visible_output_chars = 5;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This ..."
);

$this->_munger->max_visible_output_chars = 6;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This <a href=\"something.php\">is...</a>"
);

$this->_munger->max_visible_output_chars = 7;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This <a href=\"something.php\">is...</a>"
);

$this->_munger->max_visible_output_chars = 8;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This <a href=\"something.php\">is</a>..."
);

$this->_munger->max_visible_output_chars = 9;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This <a href=\"something.php\">is</a>..."
);

$this->_munger->max_visible_output_chars = 10;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This <a href=\"something.php\">is</a>..."
);

$this->_munger->max_visible_output_chars = 11;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This <a href=\"something.php\">is</a> my..."
);

$this->_munger->max_visible_output_chars = 12;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This <a href=\"something.php\">is</a> my..."
);

$this->_munger->max_visible_output_chars = 13;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This <a href=\"something.php\">is</a> my..."
);

$this->_munger->max_visible_output_chars = 14;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This <a href=\"something.php\">is</a> my..."
);

$this->_munger->max_visible_output_chars = 15;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This <a href=\"something.php\">is</a> my..."
);

$this->_munger->max_visible_output_chars = 16;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This <a href=\"something.php\">is</a> my..."
);

$this->_munger->max_visible_output_chars = 17;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first ...</p>\n"
);

$this->_munger->max_visible_output_chars = 18;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span>...</a></p>\n"
);

$this->_munger->max_visible_output_chars = 19;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span>...</a></p>\n"
);

$this->_munger->max_visible_output_chars = 20;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span>...</a></p>\n"
);

$this->_munger->max_visible_output_chars = 21;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span>...</a></p>\n"
);

$this->_munger->max_visible_output_chars = 22;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a>...</p>\n"
);

$this->_munger->max_visible_output_chars = 23;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a>...</p>\n"
);

$this->_munger->max_visible_output_chars = 24;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a>...</p>\n"
);

$this->_munger->max_visible_output_chars = 25;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a>...</p>\n"
);

$this->_munger->max_visible_output_chars = 26;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a>...</p>\n"
);

$this->_munger->max_visible_output_chars = 27;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 28;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 29;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 30;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 31;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 32;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 33;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 34;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 35;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 36;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 37;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 38;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 39;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 40;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 41;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 42;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 43;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 44;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 45;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 46;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 47;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 48;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 49;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 50;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 51;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 52;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 53;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 54;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 55;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 56;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 57;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 58;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 59;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 60;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 61;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 62;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 63;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 64;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 65;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 66;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 67;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 68;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 69;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 70;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 71;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 72;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 73;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 74;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 75;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 76;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 77;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 78;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 79;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 80;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 81;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 82;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 83;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 84;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);


$this->_munger->force_paragraphs = false;
$this->_munger->break_inside_word = true;

$this->_munger->max_visible_output_chars = 0;
$this->_run_munger_test (
  "This is my name.",
  "This is my <span class=\"highlight\">name</span>."
);

$this->_munger->max_visible_output_chars = 1;
$this->_run_munger_test (
  "This is my name.",
  "T..."
);

$this->_munger->max_visible_output_chars = 2;
$this->_run_munger_test (
  "This is my name.",
  "Th..."
);

$this->_munger->max_visible_output_chars = 3;
$this->_run_munger_test (
  "This is my name.",
  "Thi..."
);

$this->_munger->max_visible_output_chars = 4;
$this->_run_munger_test (
  "This is my name.",
  "This..."
);

$this->_munger->max_visible_output_chars = 5;
$this->_run_munger_test (
  "This is my name.",
  "This ..."
);

$this->_munger->max_visible_output_chars = 6;
$this->_run_munger_test (
  "This is my name.",
  "This i..."
);

$this->_munger->max_visible_output_chars = 7;
$this->_run_munger_test (
  "This is my name.",
  "This is..."
);

$this->_munger->max_visible_output_chars = 8;
$this->_run_munger_test (
  "This is my name.",
  "This is ..."
);

$this->_munger->max_visible_output_chars = 9;
$this->_run_munger_test (
  "This is my name.",
  "This is m..."
);

$this->_munger->max_visible_output_chars = 10;
$this->_run_munger_test (
  "This is my name.",
  "This is my..."
);

$this->_munger->max_visible_output_chars = 11;
$this->_run_munger_test (
  "This is my name.",
  "This is my ..."
);

$this->_munger->max_visible_output_chars = 12;
$this->_run_munger_test (
  "This is my name.",
  "This is my n..."
);

$this->_munger->max_visible_output_chars = 13;
$this->_run_munger_test (
  "This is my name.",
  "This is my na..."
);

$this->_munger->max_visible_output_chars = 14;
$this->_run_munger_test (
  "This is my name.",
  "This is my nam..."
);

$this->_munger->max_visible_output_chars = 15;
$this->_run_munger_test (
  "This is my name.",
  "This is my <span class=\"highlight\">name</span>..."
);

$this->_munger->max_visible_output_chars = 16;
$this->_run_munger_test (
  "This is my name.",
  "This is my <span class=\"highlight\">name</span>."
);

$this->_munger->max_visible_output_chars = 17;
$this->_run_munger_test (
  "This is my name.",
  "This is my <span class=\"highlight\">name</span>."
);

$this->_munger->max_visible_output_chars = 18;
$this->_run_munger_test (
  "This is my name.",
  "This is my <span class=\"highlight\">name</span>."
);

$this->_munger->max_visible_output_chars = 19;
$this->_run_munger_test (
  "This is my name.",
  "This is my <span class=\"highlight\">name</span>."
);


$this->_munger->max_visible_output_chars = 0;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 1;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "T..."
);

$this->_munger->max_visible_output_chars = 2;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "Th..."
);

$this->_munger->max_visible_output_chars = 3;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "Thi..."
);

$this->_munger->max_visible_output_chars = 4;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This..."
);

$this->_munger->max_visible_output_chars = 5;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This ..."
);

$this->_munger->max_visible_output_chars = 6;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This <a href=\"something.php\">i...</a>"
);

$this->_munger->max_visible_output_chars = 7;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This <a href=\"something.php\">is...</a>"
);

$this->_munger->max_visible_output_chars = 8;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This <a href=\"something.php\">is</a> ..."
);

$this->_munger->max_visible_output_chars = 9;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This <a href=\"something.php\">is</a> m..."
);

$this->_munger->max_visible_output_chars = 10;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This <a href=\"something.php\">is</a> my..."
);

$this->_munger->max_visible_output_chars = 11;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
...</p>\n"
);

$this->_munger->max_visible_output_chars = 12;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
f...</p>\n"
);

$this->_munger->max_visible_output_chars = 13;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
fi...</p>\n"
);

$this->_munger->max_visible_output_chars = 14;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
fir...</p>\n"
);

$this->_munger->max_visible_output_chars = 15;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
firs...</p>\n"
);

$this->_munger->max_visible_output_chars = 16;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first...</p>\n"
);

$this->_munger->max_visible_output_chars = 17;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first ...</p>\n"
);

$this->_munger->max_visible_output_chars = 18;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\">n...</a></p>\n"
);

$this->_munger->max_visible_output_chars = 19;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\">na...</a></p>\n"
);

$this->_munger->max_visible_output_chars = 20;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\">nam...</a></p>\n"
);

$this->_munger->max_visible_output_chars = 21;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span>...</a></p>\n"
);

$this->_munger->max_visible_output_chars = 22;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> ...</p>\n"
);

$this->_munger->max_visible_output_chars = 23;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> h...</p>\n"
);

$this->_munger->max_visible_output_chars = 24;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> he...</p>\n"
);

$this->_munger->max_visible_output_chars = 25;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> her...</p>\n"
);

$this->_munger->max_visible_output_chars = 26;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>...</p>\n"
);

$this->_munger->max_visible_output_chars = 27;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 28;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 29;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 30;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 31;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 32;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 33;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 34;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 35;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 36;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 37;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 38;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 39;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 40;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 41;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 42;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 43;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 44;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 45;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 46;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 47;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 48;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 49;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 50;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 51;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 52;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 53;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 54;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 55;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 56;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 57;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 58;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 59;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 60;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 61;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 62;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 63;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 64;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 65;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 66;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 67;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 68;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 69;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 70;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 71;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 72;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 73;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 74;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 75;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 76;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 77;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 78;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 79;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 80;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 81;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 82;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 83;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 84;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);


$this->_munger->force_paragraphs = true;
$this->_munger->break_inside_word = true;

$this->_munger->max_visible_output_chars = 0;
$this->_run_munger_test (
  "This is my name.",
  "<p>This is my <span class=\"highlight\">name</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 1;
$this->_run_munger_test (
  "This is my name.",
  "<p>T...</p>\n"
);

$this->_munger->max_visible_output_chars = 2;
$this->_run_munger_test (
  "This is my name.",
  "<p>Th...</p>\n"
);

$this->_munger->max_visible_output_chars = 3;
$this->_run_munger_test (
  "This is my name.",
  "<p>Thi...</p>\n"
);

$this->_munger->max_visible_output_chars = 4;
$this->_run_munger_test (
  "This is my name.",
  "<p>This...</p>\n"
);

$this->_munger->max_visible_output_chars = 5;
$this->_run_munger_test (
  "This is my name.",
  "<p>This ...</p>\n"
);

$this->_munger->max_visible_output_chars = 6;
$this->_run_munger_test (
  "This is my name.",
  "<p>This i...</p>\n"
);

$this->_munger->max_visible_output_chars = 7;
$this->_run_munger_test (
  "This is my name.",
  "<p>This is...</p>\n"
);

$this->_munger->max_visible_output_chars = 8;
$this->_run_munger_test (
  "This is my name.",
  "<p>This is ...</p>\n"
);

$this->_munger->max_visible_output_chars = 9;
$this->_run_munger_test (
  "This is my name.",
  "<p>This is m...</p>\n"
);

$this->_munger->max_visible_output_chars = 10;
$this->_run_munger_test (
  "This is my name.",
  "<p>This is my...</p>\n"
);

$this->_munger->max_visible_output_chars = 11;
$this->_run_munger_test (
  "This is my name.",
  "<p>This is my ...</p>\n"
);

$this->_munger->max_visible_output_chars = 12;
$this->_run_munger_test (
  "This is my name.",
  "<p>This is my n...</p>\n"
);

$this->_munger->max_visible_output_chars = 13;
$this->_run_munger_test (
  "This is my name.",
  "<p>This is my na...</p>\n"
);

$this->_munger->max_visible_output_chars = 14;
$this->_run_munger_test (
  "This is my name.",
  "<p>This is my nam...</p>\n"
);

$this->_munger->max_visible_output_chars = 15;
$this->_run_munger_test (
  "This is my name.",
  "<p>This is my <span class=\"highlight\">name</span>...</p>\n"
);

$this->_munger->max_visible_output_chars = 16;
$this->_run_munger_test (
  "This is my name.",
  "<p>This is my <span class=\"highlight\">name</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 17;
$this->_run_munger_test (
  "This is my name.",
  "<p>This is my <span class=\"highlight\">name</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 18;
$this->_run_munger_test (
  "This is my name.",
  "<p>This is my <span class=\"highlight\">name</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 19;
$this->_run_munger_test (
  "This is my name.",
  "<p>This is my <span class=\"highlight\">name</span>.</p>\n"
);


$this->_munger->max_visible_output_chars = 0;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 1;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>T...</p>\n"
);

$this->_munger->max_visible_output_chars = 2;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>Th...</p>\n"
);

$this->_munger->max_visible_output_chars = 3;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>Thi...</p>\n"
);

$this->_munger->max_visible_output_chars = 4;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This...</p>\n"
);

$this->_munger->max_visible_output_chars = 5;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This ...</p>\n"
);

$this->_munger->max_visible_output_chars = 6;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">i...</a></p>\n"
);

$this->_munger->max_visible_output_chars = 7;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is...</a></p>\n"
);

$this->_munger->max_visible_output_chars = 8;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> ...</p>\n"
);

$this->_munger->max_visible_output_chars = 9;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> m...</p>\n"
);

$this->_munger->max_visible_output_chars = 10;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my...</p>\n"
);

$this->_munger->max_visible_output_chars = 11;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
...</p>\n"
);

$this->_munger->max_visible_output_chars = 12;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
f...</p>\n"
);

$this->_munger->max_visible_output_chars = 13;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
fi...</p>\n"
);

$this->_munger->max_visible_output_chars = 14;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
fir...</p>\n"
);

$this->_munger->max_visible_output_chars = 15;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
firs...</p>\n"
);

$this->_munger->max_visible_output_chars = 16;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first...</p>\n"
);

$this->_munger->max_visible_output_chars = 17;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first ...</p>\n"
);

$this->_munger->max_visible_output_chars = 18;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\">n...</a></p>\n"
);

$this->_munger->max_visible_output_chars = 19;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\">na...</a></p>\n"
);

$this->_munger->max_visible_output_chars = 20;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\">nam...</a></p>\n"
);

$this->_munger->max_visible_output_chars = 21;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span>...</a></p>\n"
);

$this->_munger->max_visible_output_chars = 22;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> ...</p>\n"
);

$this->_munger->max_visible_output_chars = 23;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> h...</p>\n"
);

$this->_munger->max_visible_output_chars = 24;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> he...</p>\n"
);

$this->_munger->max_visible_output_chars = 25;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> her...</p>\n"
);

$this->_munger->max_visible_output_chars = 26;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>...</p>\n"
);

$this->_munger->max_visible_output_chars = 27;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 28;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 29;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 30;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 31;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 32;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 33;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 34;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 35;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 36;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 37;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 38;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 39;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 40;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 41;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 42;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 43;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 44;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 45;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 46;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 47;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 48;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 49;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 50;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 51;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 52;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 53;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 54;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 55;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 56;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 57;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 58;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 59;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 60;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 61;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 62;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 63;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 64;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 65;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 66;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 67;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 68;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 69;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 70;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 71;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 72;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 73;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 74;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 75;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 76;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 77;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 78;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 79;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 80;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 81;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 82;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 83;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);

$this->_munger->max_visible_output_chars = 84;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "<p>This <a href=\"something.php\">is</a> my<br>
first <a href=\"something.php\"><span class=\"highlight\">name</span></a> <span class=\"highlight\">here</span>.</p>\n"
);


$this->_munger->max_visible_output_chars = 0;
$this->_run_munger_test (
  "Par1\r
\r
Par2\r
\r
<pre>\r
  pre1.1\r
    pre1.2\r
    pre1.3\r
  pre1.4\r
</pre>\r
\r
Par3\r
\r
<pre>\r
\tpre2.1\r
\t\tpre2.2\r
\t\tpre2.3\r
\tpre2.4\r
</pre>\r
\r
Par4\r
\r
Par5\r
\r
\r
\r\n",
                 "<p>Par1</p>
<p>Par2</p>
<pre>  pre1.1
    pre1.2
    pre1.3
  pre1.4</pre><p>Par3</p>
<pre>\tpre2.1
\t\tpre2.2
\t\tpre2.3
\tpre2.4</pre><p>Par4</p>
<p>Par5</p>
<p>&nbsp;</p>
<p>&nbsp;</p>\n"
);

$this->_run_munger_test (
  "\r
\r
Par1\r
\r
\r
\r
\r
\r
Par2\r
\r
\r
\r
\r
<pre>\r
  pre1.1\r
    pre1.2\r
    pre1.3\r
  pre1.4\r
</pre>\r
\r
Par3\r
\r
<pre>\r
\tpre2.1\r
\t\tpre2.2\r
\t\tpre2.3\r
\tpre2.4\r
</pre>\r
\r
Par4\r
\r
Par5\r
\r\n",
                 "<p>&nbsp;</p>
<p>Par1</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>Par2</p>
<p><br>
&nbsp;</p>
<pre>  pre1.1
    pre1.2
    pre1.3
  pre1.4</pre><p>Par3</p>
<pre>\tpre2.1
\t\tpre2.2
\t\tpre2.3
\tpre2.4</pre><p>Par4</p>
<p>Par5</p>
<p>&nbsp;</p>\n"
);

$this->_run_munger_test (
  "\r
\r
Par1\r
\r
\r
\r
\r
\r
Par2\r
\r
\r
\r
\r
<pre>\r
  pre1.1\r
    pre1.2\r
    pre1.3\r
  pre1.4\r
</pre>\r
\r
\r
Par3\r
\r
\r
<pre>\r
\tpre2.1\r
\t\tpre2.2\r
\t\tpre2.3\r
\tpre2.4\r
</pre>\r
\r
\r
Par4\r
\r
Par5\r
\r\n",
                 "<p>&nbsp;</p>
<p>Par1</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>Par2</p>
<p><br>
&nbsp;</p>
<pre>  pre1.1
    pre1.2
    pre1.3
  pre1.4</pre><p><br>
Par3<br>
&nbsp;</p>
<pre>\tpre2.1
\t\tpre2.2
\t\tpre2.3
\tpre2.4</pre><p><br>
Par4</p>
<p>Par5</p>
<p>&nbsp;</p>\n"
);


$this->_munger->max_visible_output_chars = 520;

$this->_run_munger_test (
"Ich bin der Meinung, dass ich die folgende, mit peo entwickelte Logik nicht verändert habe. Unter Jet hat es funktioniert, unter SQLAny nicht.\r
\r
in Auftraege.aom:\r
\r
<code>\r
  relation\r
    class Auftrag has AuftragFuerBericht[1..1]\r
      filterExpr [[ ID <<> <<WeitereAuftraegeFuerBericht.ID ]]\r
      key IDFall\r
      key IDAuftragFuerBericht\r
      end\r
    end\r
    class Auftrag has WeitereAuftraegeFuerBericht[0..n]\r
      key IDFall\r
      key ID\r
      end\r
    end\r
  end relation\r
</code>\r
\r
in Auftraege.avm:\r
\r
<code>\r
  reference IDAuftragFuerBericht\r
    coreLink                 AuftragFuerBericht\r
    listView                 AuftragFuerBericht\r
    enabledIf                [[ >Fall>Auftraege.Count( ID ) > 1 ]]\r
    nameOfOne                'Bericht auf Auftrag'\r
  end\r
</code>\r
\r
Der Effekt müsste sein, dass bei Fällen mit mehr als einem Auftrag die jeweiligen anderen Aufträge \r
\r
unter \"Auftrag, Zusatzangaben, Bericht bei anderem Auftrag\" ausgewählt werden können.\r\n",
"<p>Ich bin der Meinung, dass ich die folgende, mit peo entwickelte Logik nicht ver&auml;ndert habe. Unter Jet hat es funktioniert, unter SQLAny nicht.</p>
<p>in Auftraege.aom:</p>
<pre><code>  relation
    class Auftrag has AuftragFuerBericht[1..1]
      filterExpr [[ ID &lt;&gt; &lt;WeitereAuftraegeFuerBericht.ID ]]
      key IDFall
      key IDAuftragFuerBericht
      end
    end
    class Auftrag has WeitereAuftraegeFuerBericht[0..n]
      key IDFall
      key ID
      end
    end
  end relation</code></pre><p>in Auftraege.avm:</p>
<pre><code>  reference IDAuftragFuerBeric...</code></pre>"
);

$this->_run_munger_test (
  "\r
Here is the <pre><c>test text</c></pre>\r
\r
Here is the <pre><c>test text</pre></c>\r\n",
  "<p><br>
<span class=\"highlight\">Here</span> is the </p>
<pre><code>test text</code></pre><p><span class=\"highlight\">Here</span> is the </p>
<pre><code>test text</code></pre>"
);

$this->_run_munger_test (
  "\r
Here is the <code>test text</code>\r
\r
Here is the <pre><c>test text</pre></code>\r\n",
  "<p><br>
<span class=\"highlight\">Here</span> is the </p>
<pre><code>test text</code></pre><p><span class=\"highlight\">Here</span> is the </p>
<pre><code>test text</code></pre>"
);

$this->_run_munger_test (
  "\r
Here is the <code>test text</code>\r
\r
Here is the <code>test text</code>\r\n",
  "<p><br>
<span class=\"highlight\">Here</span> is the </p>
<pre><code>test text</code></pre><p><span class=\"highlight\">Here</span> is the </p>
<pre><code>test text</code></pre>"
);


$this->_run_munger_test (
  "\r
<a href=\"http://www.commondreams.org/views03/0814-01.htm\" title=\"The Iraq War Could Become The Greatest Defeat In United States' History\" author=\"Tom Turnipseed\" source=\"Common Dreams\">The Iraq War...</a>\r\n",
  "<p><br>
<a href=\"http://www.commondreams.org/views03/0814-01.htm\" title=\"The Iraq War Could Become The Greatest Defeat In United States&#039; History\">The Iraq War&#8230;</a> by <cite>Tom Turnipseed</cite> (<cite><a href=\"http://www.commondreams.org/\">Common Dreams</a></cite>)<br>
&nbsp;</p>\n"
);

?>