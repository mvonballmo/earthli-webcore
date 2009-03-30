<?php

$old_show_html_output = $this->show_html_output;
$this->show_html_output = false;

$this->_run_munger_test (
  "<bq quote_style=\"none\">First Paragraph

Second Paragraph</bq>",
  "First Paragraph

Second Paragraph
"
);
$this->_run_munger_test (
  "<bq quote_style=\"single\">First Paragraph

Second Paragraph</bq>",
  "\"First Paragraph

Second Paragraph\"
"
);
$this->_run_munger_test (
  "<bq quote_style=\"multiple\">First Paragraph

Second Paragraph</bq>",
  "\"First Paragraph\"

\"Second Paragraph\"
"
);
$this->_run_munger_test (
  "<bq quote_style=\"multiple\">First Paragraph

Second Paragraph

Third Paragraph</bq>",
  "\"First Paragraph\"

\"Second Paragraph\"

\"Third Paragraph\"
"
);

$this->_run_munger_test (
  "Bush is clearly not living in the world we live in. He doesn't lie; it's just that, when he talks, he's not describing the world we live in. In fact, he welcomes us all to join him in his fervent worship of a simplistic black-and-white world where saying something makes it true. A few examples:",
  "Bush is clearly not living in the world we live in. He doesn't lie; it's just
that, when he talks, he's not describing the world we live in. In fact, he
welcomes us all to join him in his fervent worship of a simplistic
black-and-white world where saying something makes it true. A few examples:
"
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
</ol>",
  "Bush is clearly not living in the world we live in. He doesn't lie; it's just
that, when he talks, he's not describing the world we live in. In fact, he
welcomes us all to join him in his fervent worship of a simplistic
black-and-white world where saying something makes it true. A few examples:

   1. Bush is clearly not living in the world we live in. He doesn't lie; it's
      just that, when he talks, he's not describing the world we live in.
      1. In fact, he welcomes us all to join him in his fervent worship of a
         simplistic black-and-white world where saying something makes it true.
         A few examples:
      2. ray tell, does that number come from? Does anyone even care anymore
         when Bush pulls a number out of his ass? How do we know how many people
        * when he talks, he's not describing the world we live in. In fact, he
          welcomes Bush is clearly not living in the world we live in. He
          doesn't lie; it's just that, 
        * when he talks, he's not describing the world we live in. In fact, he
          welcomes 
          * America oozes from it's pores as it strides through the benighted
            world, bestowing freedom and compassion with a kind paternal hand.
            Bush touted their recent elections as a
        * when he talks, he's not describing the world we live in. In fact, he
          welcomes
           1. America oozes from it's pores as it strides through the benighted
              world, bestowing freedom and compassion with a kind paternal hand.
              Bush touted their recent elections as a
      3. are in Al Qaeda? How do we even know if it exists, per se? Doesn't
         matter, we've caught 75% a
"
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
  "Bush is clearly not living in the world we live in. He doesn't lie; it's just
that, when he talks, he's not describing the world we live in. In fact, he
welcomes us all to join him in his fervent worship of a simplistic
black-and-white world where saying something makes it true. A few examples:

   1. Bush is clearly not living in the world we live in. He doesn't lie; it's
      just that, when he talks, he's not describing the world we live in.
      1. In fact, he welcomes us all to join him in his fervent worship of a
         simplistic black-and-white world where saying something makes it true.
         A few examples:
      2. ray tell, does that number come from? Does anyone even care anymore
         when Bush pulls a number out of his ass? How do we know how many people
        * when he talks, he's not describing the world we live in. In fact, he
          welcomes Bush is clearly not living in the world we live in. He
          doesn't lie; it's just that, 
        * when he talks, he's not describing the world we live in. In fact, he
          welcomes 
          * America oozes from it's pores as it strides through the benighted
            world, bestowing freedom and compassion with a kind paternal hand.
            Bush touted their recent elections as a
        * when he talks, he's not describing the world we live in. In fact, he
          welcomes
           1. America oozes from it's pores as it strides through the benighted
              world, bestowing freedom and compassion with a kind paternal hand.
              Bush touted their recent elections as a
      3. are in Al Qaeda? How do we even know if it exists, per se? Doesn't
         matter, we've caught 75% a
   2. Bush is clearly not living in the world we live in. He doesn't lie; it's
      just that, when he talks, he's not describing the world we live in. In
      fact, he welcomes us all to join him in his fervent worship of a
      simplistic black-and-white world where saying something makes it true. A
      few examples:

Al Qaeda

   Apparently, the war on terror is going unbelievably well: it's a catastrophic
   success. Bush noted in the second debate that \"we're bringing Al Qaida to
   justice. Seventy five percent of them have been brought to justice.\" Where,
   pray tell, does that number come from? Does anyone even care anymore when
   Bush pulls a number out of his ass? How do we know how many people are in Al
   Qaeda? How do we even know if it exists, per se? Doesn't matter, we've caught
   75% and by the end of his next 4 years, we'll have easily cleaned up the
   rest. Then we can all celebrate in a world without evil.

The Taliban

   In Bush's fairy-tale world, the Taliban also no longer exist. Because we
   vanquished them. In the real world, they didn't stay vanquished for long,
   although they have had to cede power to the warlords throughout most of
   Afghanistan, where the drug trade has increased by approximately 400 million
   percent.

Afghanistan

   This is a picture-perfect example of a country that benefitted from the
   freedom that America oozes from it's pores as it strides through the
   benighted world, bestowing freedom and compassion with a kind paternal hand.
   Bush touted their recent elections as an astounding success; \"Afghanistan's
   election\"
   <http://www.registerguard.com/news/2004/10/19/ed.edit.afghanistan.phn.1019.html>
   (Afghanistan's election: U.S., allies must fulfill long-term commitment)
   gives a brief run-down of the country's situation:
   
   \"warlords have gained power in ... provinces where their private militias
   hold sway. The opium trade, once quashed under the Taliban, has been revived
   on a massive scale. Violence has driven most nonprofit organizations from the
   countryside. Health experts warn that 70 percent of Afghan people are
   malnourished; only 13 percent have access to potable water and sanitation.
   (emphasis added)\"
   
   Hold on a minute. Doctors without borders has bailed on the country as too
   dangerous, but their elections are more than just an idle gesture? How can
   that be? So the people of Afghanistan (those than aren't too starved to even
   get to a polling station, which would be 30% of them) voted for the president
   of Kabul (\"outside of Kabul, where 80 percent of Afghans live ... Karzai's
   control is tenuous or nonexistent\")? Wow ... massive progress. A resounding
   blow for freedom. All signs indicate that the timing of this election once
   again, suspiciously, benefits Bush, as the article also notes that a lot of
   work needs to be done to make sure that \"this election is not to be
   Afghanistan's last\". 

Diplomacy

   During the second debate, Bush defended his record as a war president,
   declaring that \"obviously we hope that diplomacy works before you ever use
   force. The hardest decision a president makes is ever to use force.\" That
   statement alone proves there is no God, else he would have been struck dead
   by 10,000 simultaneous lightning blasts. Later he said that \"I tried
   diplomacy, went to the United Nations.\" Just showing up in a building is not
   diplomacy, jackass. That's called lip service. Just like coming up with new,
   ever more fantastical reasons to attack Iraq each time one is shot down
   doesn't count as \"trying to avoid a war\".

Weapons of Mass Destruction

   The recently-issued Duelfer report about WMDs is huge. Almost everyone who
   reads it or heard his testimony sees overwhelming proof that the sanctions
   worked and that, after 1995, Saddam had no WMDs left. The fact that, in the
   last 20 months, no weapons have been found, is also somewhat damning to the
   Bush administration's justifications for war.
   
   Bush to the rescue! He goes the extra mile and notes only that Saddam (who
   is an evil bastard, we all agree), wasn't playing nice with the UN sanctions.
   In fact, he was \"gaming the oil-for-food program to get rid of sanctions ...
   [because] ... [h]e wanted to restart his weapons programs\". For Bush,
   \"[t]hat's what the Duelfer report showed. He was deceiving the inspectors.\"
   
   Period. That's it. There are no other conclusions to draw from the report
   except that Bush was right all along. Of course, Saddam didn't have any
   weapons, nor any means to procure them. However, since the sanctions (which
   neither candidate condemns for the human-rights disaster it was) didn't
   magically turn Saddam into a nice guy, they obviously weren't enough. See?
   You see how it was right to go to Iraq? When you look at 7 words out of a
   10000 page document and ignore all other physical evidence? You see how a
   powerful faith can truly move mountains?

Election in Iraq

   These will happen in January and they will mean something. The shining
   example of Afghanistan will lead Iraq out of the darkness.

Coalition of the Willing

   Look ... the name itself is completely uninspiring. Remember how disgusted
   Bush was that Kerry \"forgot Poland\"? Too bad Bush wasted all of that time
   learning how to pronounce Aleksander Kwasniewski because they have in the
   meantime also withdrawn their troops. Bush will tell you all day long that
   the coalition is composed of dozens of strong countries. Kerry nailed him to
   the wall with these beauties though: \"Mr. President, countries are leaving
   the coalition, not joining. Eight countries have left it.\" and this one was
   just priceless:
   
   \"If Missouri, just given the number of people from Missouri who are in the
   military over there today, were a country, it would be the third largest
   country in the coalition, behind Great Britain and the United States.\"
   
   Ouch! Naturally, Bush's version of reality survived intact.

Missile Defense Shield

   And finally, a fantasy that many other presidents and candidates also slobber
   on about. Here's where Bush doesn't even attempt to technically justify the
   shield -- he just notes that his opponent is opposed to it and smirks. This,
   for a seemingly impossibly consistent 45% of America, is enough proof that
   the program is good. Never mind that there is no way it will ever work; never
   mind that Reagan, one of the century's leading thinkers, hatched this
   hare-brained idea; never mind that only scientists on a Rebulican
   think-tank's payroll will offer any comment other than gut-laughing. never
   mind that it's obvious pandering to his strongest corporate supporters: the
   military-industrial complex.
   
   Kerry, believe it or not, is opposed to it and plans to shut the program
   down (if possible, I might add ... it might be too much a part of the budget
   culture by now). There are rumors that work is underway to provide a
   several-missile deployment in California as \"proof\" that Kim Jong Il is not a
   threat. Two birds with one subterfuge would be quite a feather in its cap for
   this administration.

Bush is clearly not living in the world we live in. He doesn't lie; it's just
that, when he talks, he's not describing the world we live in. In fact, he
welcomes us all to join him in his fervent worship of a simplistic
black-and-white world where saying something makes it true. A few examples:
Bush is clearly not living in the world we live in. He doesn't lie; it's just
that, when he talks, he's not describing the world we live in. In fact, he
welcomes us all to join him in his fervent worship of a simplistic
black-and-white world where saying something makes it true. A few examples:
"
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
  "Bush is clearly not living in the world we live in. He doesn't lie; it's just
that, when he talks, he's not describing the world we live in. In fact, he
welcomes us all to join him in his fervent worship of a simplistic
black-and-white world where saying something makes it true. A few examples:

   1. Bush is clearly not living in the world we live in. He doesn't lie; it's
      just that, when he talks, he's not describing the world we live in.
      1. In fact, he welcomes us all to join him in his fervent worship of a
         simplistic black-and-white world where saying something makes it true.
         A few examples:
      2. ray tell, does that number come from? Does anyone even care anymore
         when Bush pulls a number out of his ass? How do we know how many people
        * when he talks, he's not describing the world we live in. In fact, he
          welcomes Bush is clearly not living in the world we live in. He
          doesn't lie; it's just that, 
        * when he talks, he's not describing the world we live in. In fact, he
          welcomes 
          * America oozes from it's pores as it strides through the benighted
            world, bestowing freedom and compassion with a kind paternal hand.
            Bush touted their recent elections as a
        * when he talks, he's not describing the world we live in. In fact, he
          welcomes
           1. America oozes from it's pores as it strides through the benighted
              world, bestowing freedom and compassion with a kind paternal hand.
              Bush touted their recent elections as a
      3. are in Al Qaeda? How do we even know if it exists, per se? Doesn't
         matter, we've caught 75% a
   2. Bush is clearly not living in the world we live in. He doesn't lie; it's
      just that, when he talks, he's not describing the world we live in. In
      fact, he welcomes us all to join him in his fervent worship of a
      simplistic black-and-white world where saying something makes it true. A
      few examples:

Al Qaeda

   Apparently, the war on terror is going unbelievably well: it's a catastrophic
   success. Bush noted in the second debate that \"we're bringing Al Qaida to
   justice. Seventy five percent of them have been brought to justice.\" Where,
   pray tell, does that number come from? Does anyone even care anymore when
   Bush pulls a number out of his ass? How do we know how many people are in Al
   Qaeda? How do we even know if it exists, per se? Doesn't matter, we've caught
   75% and by the end of his next 4 years, we'll have easily cleaned up the
   rest. Then we can all celebrate in a world without evil.

The Taliban

   In Bush's fairy-tale world, the Taliban also no longer exist. Because we
   vanquished them. In the real world, they didn't stay vanquished for long,
   although they have had to cede power to the warlords throughout most of
   Afghanistan, where the drug trade has increased by approximately 400 million
   percent.

Afghanistan

   This is a picture-perfect example of a country that benefitted from the
   freedom that America oozes from it's pores as it strides through the
   benighted world, bestowing freedom and compassion with a kind paternal hand.
   Bush touted their recent elections as an astounding success; \"Afghanistan's
   election\"
   <http://www.registerguard.com/news/2004/10/19/ed.edit.afghanistan.phn.1019.html>
   (Afghanistan's election: U.S., allies must fulfill long-term commitment)
   gives a brief run-down of the country's situation:
   
   \"warlords have gained power in ... provinces where their private militias
   hold sway. The opium trade, once quashed under the Taliban, has been revived
   on a massive scale. Violence has driven most nonprofit organizations from the
   countryside. Health experts warn that 70 percent of Afghan people are
   malnourished; only 13 percent have access to potable water and sanitation.
   (emphasis added)\"
   
   Hold on a minute. Doctors without borders has bailed on the country as too
   dangerous, but their elections are more than just an idle gesture? How can
   that be? So the people of Afghanistan (those than aren't too starved to even
   get to a polling station, which would be 30% of them) voted for the president
   of Kabul (\"outside of Kabul, where 80 percent of Afghans live ... Karzai's
   control is tenuous or nonexistent\")? Wow ... massive progress. A resounding
   blow for freedom. All signs indicate that the timing of this election once
   again, suspiciously, benefits Bush, as the article also notes that a lot of
   work needs to be done to make sure that \"this election is not to be
   Afghanistan's last\". 
   
   
      1. Bush is clearly not living in the world we live in. He doesn't lie;
         it's just that, when he talks, he's not describing the world we live
         in.
         1. In fact, he welcomes us all to join him in his fervent worship of a
            simplistic black-and-white world where saying something makes it
            true. A few examples:
         2. ray tell, does that number come from? Does anyone even care anymore
            when Bush pulls a number out of his ass? How do we know how many
            people
           * when he talks, he's not describing the world we live in. In fact,
             he welcomes Bush is clearly not living in the world we live in. He
             doesn't lie; it's just that, 
           * when he talks, he's not describing the world we live in. In fact,
             he welcomes 
             * America oozes from it's pores as it strides through the benighted
               world, bestowing freedom and compassion with a kind paternal
               hand. Bush touted their recent elections as a
           * when he talks, he's not describing the world we live in. In fact,
             he welcomes
              1. America oozes from it's pores as it strides through the
                 benighted world, bestowing freedom and compassion with a kind
                 paternal hand. Bush touted their recent elections as a
         3. are in Al Qaeda? How do we even know if it exists, per se? Doesn't
            matter, we've caught 75% a
      2. Bush is clearly not living in the world we live in. He doesn't lie;
         it's just that, when he talks, he's not describing the world we live
         in. In fact, he welcomes us all to join him in his fervent worship of a
         simplistic black-and-white world where saying something makes it true.
         A few examples:
   
   

Diplomacy

   During the second debate, Bush defended his record as a war president,
   declaring that \"obviously we hope that diplomacy works before you ever use
   force. The hardest decision a president makes is ever to use force.\" That
   statement alone proves there is no God, else he would have been struck dead
   by 10,000 simultaneous lightning blasts. Later he said that \"I tried
   diplomacy, went to the United Nations.\" Just showing up in a building is not
   diplomacy, jackass. That's called lip service. Just like coming up with new,
   ever more fantastical reasons to attack Iraq each time one is shot down
   doesn't count as \"trying to avoid a war\".

Weapons of Mass Destruction

   The recently-issued Duelfer report about WMDs is huge. Almost everyone who
   reads it or heard his testimony sees overwhelming proof that the sanctions
   worked and that, after 1995, Saddam had no WMDs left. The fact that, in the
   last 20 months, no weapons have been found, is also somewhat damning to the
   Bush administration's justifications for war.
   
   Bush to the rescue! He goes the extra mile and notes only that Saddam (who
   is an evil bastard, we all agree), wasn't playing nice with the UN sanctions.
   In fact, he was \"gaming the oil-for-food program to get rid of sanctions ...
   [because] ... [h]e wanted to restart his weapons programs\". For Bush,
   \"[t]hat's what the Duelfer report showed. He was deceiving the inspectors.\"
   
   Period. That's it. There are no other conclusions to draw from the report
   except that Bush was right all along. Of course, Saddam didn't have any
   weapons, nor any means to procure them. However, since the sanctions (which
   neither candidate condemns for the human-rights disaster it was) didn't
   magically turn Saddam into a nice guy, they obviously weren't enough. See?
   You see how it was right to go to Iraq? When you look at 7 words out of a
   10000 page document and ignore all other physical evidence? You see how a
   powerful faith can truly move mountains?

Election in Iraq

   These will happen in January and they will mean something. The shining
   example of Afghanistan will lead Iraq out of the darkness.

Coalition of the Willing

   Look ... the name itself is completely uninspiring. Remember how disgusted
   Bush was that Kerry \"forgot Poland\"? Too bad Bush wasted all of that time
   learning how to pronounce Aleksander Kwasniewski because they have in the
   meantime also withdrawn their troops. Bush will tell you all day long that
   the coalition is composed of dozens of strong countries. Kerry nailed him to
   the wall with these beauties though: \"Mr. President, countries are leaving
   the coalition, not joining. Eight countries have left it.\" and this one was
   just priceless:
   
   \"If Missouri, just given the number of people from Missouri who are in the
   military over there today, were a country, it would be the third largest
   country in the coalition, behind Great Britain and the United States.\"
   
   Ouch! Naturally, Bush's version of reality survived intact.

Missile Defense Shield

   And finally, a fantasy that many other presidents and candidates also slobber
   on about. Here's where Bush doesn't even attempt to technically justify the
   shield -- he just notes that his opponent is opposed to it and smirks. This,
   for a seemingly impossibly consistent 45% of America, is enough proof that
   the program is good. Never mind that there is no way it will ever work; never
   mind that Reagan, one of the century's leading thinkers, hatched this
   hare-brained idea; never mind that only scientists on a Rebulican
   think-tank's payroll will offer any comment other than gut-laughing. never
   mind that it's obvious pandering to his strongest corporate supporters: the
   military-industrial complex.
   
   Kerry, believe it or not, is opposed to it and plans to shut the program
   down (if possible, I might add ... it might be too much a part of the budget
   culture by now). There are rumors that work is underway to provide a
   several-missile deployment in California as \"proof\" that Kim Jong Il is not a
   threat. Two birds with one subterfuge would be quite a feather in its cap for
   this administration.

Bush is clearly not living in the world we live in. He doesn't lie; it's just
that, when he talks, he's not describing the world we live in. In fact, he
welcomes us all to join him in his fervent worship of a simplistic
black-and-white world where saying something makes it true. A few examples:
Bush is clearly not living in the world we live in. He doesn't lie; it's just
that, when he talks, he's not describing the world we live in. In fact, he
welcomes us all to join him in his fervent worship of a simplistic
black-and-white world where saying something makes it true. A few examples:
"
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
Bush thinks that young people (teens on up) should be able to invest that money in the stock market instead. They are far more likely to invest it in an iPod or 20\" rims.\r
</ul>\r
Definition of work\r
<ul>\r
Kerry, several times, in fact, mentioned that he wants programs to have enough money to get their job done\r
Bush is happy to call an increase an improvement, no matter what. (Pell grants here are an example -- Bush's administration saw one million more of them distributed, but each grant is, on average only half of what it was four years ago)\r
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
  "This is the last installment of \"stuff I learned during the debates\".
Unattributed quotes below are straight from the transcripts, found at \"Debate
#2\" <http://www.debates.org/pages/trans2004c.html> (\"Rage in the Cage\") and
\"Debate #3\" <http://www.debates.org/pages/trans2004d.html> (\"Bloodmatch -- back
for more Blood\").

[Head to Head]

Patriot Act

     * Kerry says he supports the Patriot Act. He also says it needs to be
       pretty much massively rewritten, but he doesn't have the gonads to say
       it's a piece of crap, because that offends too many of the wrong people.
     * Bush doesn't think the Patriot Act has affected citizen's rights

The Internet(s)

     * Kerry mentioned his web site several times and directed people there for
       more information
     * Bush talks about \"the Internets\". WTF? Has he ever even used a computer?

Gay marriage

     * Kerry doesn't support gay marriage, but supports partnerships, whatever
       that means. We already have a legal definition for when people want to
       spend their lives together: why do you need to make another one?
     * Bush supports gay marriage if it's \"between a man and a woman\".

Ronald Reagan

     * Kerry loves him. (WTF is up with that?)
     * Bush probably loves him even more

Faith-based programs

     * Kerry believes in them strongly and will support and increase them
     * Bush invented the damned things and would like to funnel all federal
       dollars into them, so you need to take a Jesus loyaltly oath to get a
       piece of bread in a soup kitchen.

Abortion

     * Kerry spent several minutes talking about abortion without ever (not
       once) actually saying the word. He seems to probably support a woman's
       right to choose, but he's so heavily politicized, it's kind of hard to
       tell.
     * Bush thinks abortion should be outlawed, but will settle for making
       abortion so difficult, socially sitgmatizing and painful that no one gets
       them anymore. Rues every dollar of federal money spent on any program
       that ever uttered the word abortion.

9/11

     * Kerry thinks this is a reason for \"hunting and killing terrorists\"
     * Bush thinks this is a reason for everything.

Terrorists

     * Kerry thinks that terrorists are mindless, soul-eating machines, born to
       kill without reason and with an undying and unexplainable hatred of
       America. He thinks we should protect our borders and industrial centers
       better.
     * Bush thinks that terrorists are mindless, soul-eating machines, born to
       kill without reason and with an undying and unexplainable hatred of
       America. He thinks we should \"stay on the offensive\" ('cause that's
       worked out great so far)

Changing his mind

     * Kerry seems to alter his opinions when the facts have changed
     * Bush never, ever, ever reconsiders anything

Health Care

     * Kerry sees this as the country's greatest failure, pointing out that
       every other first world country has a system
     * Bush doesn't care how those other pansies are doing it and thinks the
       current system works just fine

Jobs

     * Kerry thinks we need to stop tax incentives that encourage American firms
       from hiring outside of the US. Thinks companies should have to pay a
       living wage for all jobs and wants to raise the minimum wage. Seems to
       support prosecuting companies that hire illegals rather than the illegals
       themselves.
     * Bush thinks that a job is a job, regardless of how much it pays; also
       thinks that migrant workers should be allowed to do jobs that \"no
       American wants to do\". Is happy with the market regulating pay levels,
       even if that doesn't result in a living wage. He \"will talk about the 1.7
       million jobs created since the summer of 2003, and will say that the
       economy is \"strong and getting stronger.\" That's like boasting about
       getting a D on your final exam, when you flunked the midterm and needed
       at least a C to pass the course.\" (\"Checking the Facts, in Advance\" by
       Paul Krugman <http://pkarchive.org/column/101204.html>)

Social Security

     * Kerry thinks we should leave it alone and stop thinking up ways to
       plunder it
     * Bush thinks that young people (teens on up) should be able to invest that
       money in the stock market instead. They are far more likely to invest it
       in an iPod or 20\" rims.

Definition of work

     * Kerry, several times, in fact, mentioned that he wants programs to have
       enough money to get their job done
     * Bush is happy to call an increase an improvement, no matter what. (Pell
       grants here are an example -- Bush's administration saw one million more
       of them distributed, but each grant is, on average only half of what it
       was four years ago)

The Military

     * Kerry thinks we're overextended, but also thinks we should have sent
       more. Thinks we should increase the military by 40,000 people. Perhaps
       it's jobs program?
     * Bush says there will not be a draft, because everything is hunky dorey.

Religion

     * Kerry bases his entire life around religion and his faith (\"My faith
       affects everything that I do, in truth. ... I think that everything you
       do in public life has to be guided by your faith, affected by your
       faith...\")
     * Bush bases his entire life around religion and his faith (\"First, my
       faith plays a lot -- a big part in my life. And that's, when I was
       answering that question, what I was really saying to the person was that
       I pray a lot. And I do.\")

[Kerry Quotes]

\"But you know what we also need to do as Americans is never let the terrorists
change the Constitution of the United States in a way that disadvantages our
rights.\"

\"Being lectured by the president on fiscal responsibility is a little bit like
Tony Soprano talking to me about law and order in this country.\"

[Conclusions]

Kerry is

  * a politician, through and through. He stands for something if it doesn't get
    in the way of his career
  * not going to fix a lot of things that are wrong with America, but has the
    taken the big step of admitting that there are things wrong with America
  * Talks a lot about war; will stay in Iraq; will hold the course in Israel
  * Also talks a lot about domestic programs and actually addresses problems in
    a meaningful and halfway-honest way (for a politician)

Bush is

  * Petulant, whiny, defensive, occasionally hostile; seems to beg you to agree
    with him
  * Self-contradicting:
     1. He says we can't get cheaper meds from Canada (as he promised four years
        ago) because they \"might not be safe\". When asked what he's going to do
        about the shortage of flu vaccine, he suggests Canada as our last hope.
     2. Does everything possible to protect a baby before it's born; does
        everything possible to prevent paying for a single thing once it takes
        its first breath.
  * Is too stupid to even realize that the \"you can run, but you can't hide\"
    slogan backfires horribly because the first guy he applied it to, Osama Bin
    Laden, is still hiding quite nicely.
  * Seems to be for the Dred Scott decision, which disallowed citizen's rights
    to any black person, regardless of slave/free status. Is too stupid to
    realize that he's too stupid to talk about history in any depth or
    meaningful way.
  * If he's forced to admit that something's not optimal, he claims he
    \"inherited it\" from Clinton.
"
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
  "I know I've seen this one before, but I got this via email and was kind of
struck by some of these numbers (highlighted below).

If we could shrink the earth's population to a village of precisely 100 people,
with all the existing human ratios remaining the same, it would look something
like the following*:

-----------
| Hungry? |
-----------

57 Asians
21 Europeans
14 from the Western Hemisphere, both North and South (Does this include all of
Europe? I think so...)
8 Africans
52 would be female
48 would be male
70 would be nonwhite
70 would be non-Christian (a bit of a wake-up call for Bush's base, methinks)
89 would be heterosexual
11 would be homosexual
6 people would possess 59% of the entire world's wealth
All 6 would be from the United States
80 would live in substandard housing
70 would be unable to read
50 would suffer from malnutrition
1 would be near death;
1 would be near birth
1 would have a college education
1 would own a computer (a reminder that, relative to the world, you and everone
you know is upper class)
-----------

*Since the numbers are so massively constrained, rounding errors and the law of
averages will cause some interesting numbers to come out. There are rich people
living outside of the US -- just not a lot relative to those within the US. I
can't verify most of these statistics. If anyone sees some that are out of
whack, let me know.
"
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
</dl>\r
",
  "
Testing definition lists.

Definition 1

   This is the text of the definition for 1.

Definition 2

   This is the text of the definition for 2.

Definition 3

   This is the text of the definition for 3.

Definition 4

   This is the text of the definition for 4.

"
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
</dl>\r
",
  "
Testing definition lists.

Definition 1

   This is the text of the definition for 1.

Definition 2

   This is the text of the definition for 2.

Definition 3

   This is the text of the definition for 3.

Definition 4

   This is the text of the definition for 4.

"
);

$this->_run_munger_test (
  "\r
Testing headings.\r
\r
<h level=\"1\">H1 heading</h>\r
Here's some text under this heading\r
<ul>\r
  Normal title\r
</ul>\r
Here's some text under this list.\r
<h level=\"high\">Bogus heading</h>\r
More text.\r
<h>Multi-line\r
heading</h>\r
More text II.\r
",
  "
Testing headings.

[H1 heading]

Here's some text under this heading

  * Normal title

Here's some text under this list.

[Bogus heading]

More text.

[Multi-line
heading]

More text II.
"
);

$this->_run_munger_test (
  "\r
Testing headings.\r
\r
<h level=\"1\">H1 heading</h>\r
Here's some text under this heading\r
<ul>Normal title</ul>\r
Here's some text under this list.\r
<h level=\"high\">Bogus heading</h>\r
More text.\r
<h>Multi-line\r
heading</h>\r
More text II.\r
",
  "
Testing headings.

[H1 heading]

Here's some text under this heading

  * Normal title

Here's some text under this list.

[Bogus heading]

More text.

[Multi-line
heading]

More text II.
"
);

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
\r
",
  "
Testing headings.


Testing headings.

[H1 heading]

Here's some text under this heading

[Normal title]

Here's some text under this heading (level 3).

[Bogus heading]




[Multi-line
heading]

"
);

$this->_run_munger_test (
  "This<ul>
1.1
<ul>
2.1
2.2
</ul>
1.2
</ul>
",
  "This

  * 1.1
   * 2.1
   * 2.2
  * 1.2

"
);

$this->_run_munger_test (
  "This<ul>

1.1

<ul>

2.1

2.2

</ul>

1.2

</ul>

",
  "This

  * 
  * 1.1
  * 
   * 
   * 2.1
   * 
   * 2.2
   * 
  * 
  * 1.2
  * 

"
);

$this->_run_munger_test (
  "This<ul>
1.1<ul>
2.1
2.2
</ul>
1.2
</ul>
",
  "This

  * 1.1
   * 2.1
   * 2.2
    1.2

"
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
</ul>
",
  "This

  * 1.1
   * 2.1
      * 3.1
        * 4.1
        * 4.2
      * 3.2
   * 2.2
  * 1.2

"
);

$this->_run_munger_test (
  "This<ul>
1.1
<ul>
2.1
<ul>
3.1
3.2
</ul>
2.2
</ul>
1.2
</ul>
",
  "This

  * 1.1
   * 2.1
      * 3.1
      * 3.2
   * 2.2
  * 1.2

"
);

$this->_run_munger_test (
  "

This

<ul>
1.1
<ul>
2.1
<ul>
3.1
3.2
</ul>
2.2
</ul>
1.2
</ul>

",
  "

This

  * 1.1
   * 2.1
      * 3.1
      * 3.2
   * 2.2
  * 1.2

"
);

$this->_run_munger_test (
  "

This
<ul>
1.1
<ul>
2.1
<ul>
3.1
3.2
</ul>
2.2
</ul>
1.2
</ul>

",
  "

This

  * 1.1
   * 2.1
      * 3.1
      * 3.2
   * 2.2
  * 1.2

"
);

$this->_run_munger_test (
  "
This
<ul>
1.1
<ul>
2.1
<ul>
3.1
3.2
</ul>
2.2
</ul>
1.2
</ul>

Final Text.
",
  "
This

  * 1.1
   * 2.1
      * 3.1
      * 3.2
   * 2.2
  * 1.2

Final Text.
"
);

$this->_run_munger_test (
  "

This
<ul>
1.1
<ul>
2.1
<ul>
3.1
3.2
</ul>
2.2
</ul>
1.2
</ul>
Final Text.
",
  "

This

  * 1.1
   * 2.1
      * 3.1
      * 3.2
   * 2.2
  * 1.2

Final Text.
"
);

$this->_run_munger_test (
  "

This

<ul>
1.1
<ul>
2.1
<ul>
3.1
3.2
</ul>
2.2
</ul>
1.2
</ul>

Final Text.
",
  "

This

  * 1.1
   * 2.1
      * 3.1
      * 3.2
   * 2.2
  * 1.2

Final Text.
"
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
</ul>\r
",
  "Stuff:

  * 1.1
  * 1.2
   * 2.1
      * 3.1
      * 3.2
      * 3.3
      * 
      2.2
   * 2.3
      * 3.1
      * 3.2
        * 4.1
    Par3
  * Par4
  * Par5

"
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
\r
",
  "Stuff:

  * Howdy
  pre1.1
    pre1.2

     1. pre2.1
     2. pre2.2
        1. pre3.1
        2. pre3.2
     3. pre2.3
    pre1.3
  pre1.4
  * 1.1
  * 1.2
   * 2.1
      * 3.1
      * 3.2
      * 3.3
      * 
      2.2
   * 2.3
      * 3.1
      * 3.2
        * 4.1
    Par3
\tpre2.1
\t\tpre2.2
\t\tpre2.3
\tpre2.4
    
  * Par4
  * Par5

"
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
</ol>\r
",
  "
Put your test text here.

  * one/one
   * two/one
   * two/two
      * three/one
      two/three
  * one/two
  * one/three

   1. one/one
      1. two/one
      2. two/two
         1. three/one
         two/three
   2. one/two
   3. one/three

"
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
</ol>\r
",
  "
Put your test text here.

  * one/one
   * two/one
   * two/two
      * three/one
   * two/three
  * one/two
  * one/three

   1. one/one
      1. two/one
      2. two/two
         1. three/one
      3. two/three
   2. one/two
   3. one/three

"
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
<span class=\"notes\">*Fish sauce is made from fermented salted fish.  It can be found in Asian food stores and requires no refrigeration after opening.</span>\r
",
  "
Noodles

  * 2 qt water
  * 3/4 lb mung bean sprouts
  * 6 oz rice noodles (1/4-inch wide)

Sauce

  * 3 tb fresh lime juice
  * 3 tb catsup
  * 1 tb brown sugar
  * 1/4 c  fish sauce* or soy sauce

Remaining Ingredients

  * 3 tb peanut oil or vegetable oil
  * 3 to 4 cloves garlic; minced or pressed
  * 1 tb fresh chile, minced or 1 1/2 ts crushed red pepper flakes
  * 2 c  carrots, grated
  * 4 lg eggs, lightly beaten with a pinch of salt
  * 2/3 c peanuts, chopped
  * 6 to 8 scallions, chopped (about 1 cup)

*Fish sauce is made from fermented salted fish.  It can be found in Asian food
stores and requires no refrigeration after opening.
"
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
</ul><span class=\"notes\">*Fish sauce is made from fermented salted fish.  It can be found in Asian food stores and requires no refrigeration after opening.</span>\r
",
  "
Noodles

  * 2 qt water
  * 3/4 lb mung bean sprouts
  * 6 oz rice noodles (1/4-inch wide)

Sauce

  * 3 tb fresh lime juice
  * 3 tb catsup
  * 1 tb brown sugar
  * 1/4 c  fish sauce* or soy sauce

Remaining Ingredients

  * 3 tb peanut oil or vegetable oil
  * 3 to 4 cloves garlic; minced or pressed
  * 1 tb fresh chile, minced or 1 1/2 ts crushed red pepper flakes
  * 2 c  carrots, grated
  * 4 lg eggs, lightly beaten with a pinch of salt
  * 2/3 c peanuts, chopped
  * 6 to 8 scallions, chopped (about 1 cup)

*Fish sauce is made from fermented salted fish.  It can be found in Asian food
stores and requires no refrigeration after opening.
"
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
<span class=\"notes\">*Fish sauce is made from fermented salted fish.  It can be found in Asian food stores and requires no refrigeration after opening.</span>\r
",
  "
Noodles

  * 2 qt water
  * 3/4 lb mung bean sprouts
  * 6 oz rice noodles (1/4-inch wide)

Sauce

  * 3 tb fresh lime juice
  * 3 tb catsup
  * 1 tb brown sugar
  * 1/4 c  fish sauce* or soy sauce

Remaining Ingredients

  * 3 tb peanut oil or vegetable oil
  * 3 to 4 cloves garlic; minced or pressed
  * 1 tb fresh chile, minced or 1 1/2 ts crushed red pepper flakes
  * 2 c  carrots, grated
  * 4 lg eggs, lightly beaten with a pinch of salt
  * 2/3 c peanuts, chopped
  * 6 to 8 scallions, chopped (about 1 cup)

*Fish sauce is made from fermented salted fish.  It can be found in Asian food
stores and requires no refrigeration after opening.
"
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
<span class=\"notes\">*Fish sauce is made from fermented salted fish.  It can be found in Asian food stores and requires no refrigeration after opening.</span>\r
",
  "
Noodles

  * 2 qt water
  * 3/4 lb mung bean sprouts
  * 6 oz rice noodles (1/4-inch wide)

Sauce

  * 3 tb fresh lime juice
  * 3 tb catsup
  * 1 tb brown sugar
  * 1/4 c  fish sauce* or soy sauce

Remaining Ingredients

  * 3 tb peanut oil or vegetable oil
  * 3 to 4 cloves garlic; minced or pressed
  * 1 tb fresh chile, minced or 1 1/2 ts crushed red pepper flakes
  * 2 c  carrots, grated
  * 4 lg eggs, lightly beaten with a pinch of salt
  * 2/3 c peanuts, chopped
  * 6 to 8 scallions, chopped (about 1 cup)

*Fish sauce is made from fermented salted fish.  It can be found in Asian food
stores and requires no refrigeration after opening.
"
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
<span class=\"notes\">*Fish sauce is made from fermented salted fish.  It can be found in Asian food stores and requires no refrigeration after opening.</span>\r
",
  "

Noodles


  * 2 qt water
  * 3/4 lb mung bean sprouts
  * 6 oz rice noodles (1/4-inch wide)


Sauce


  * 3 tb fresh lime juice
  * 3 tb catsup
  * 1 tb brown sugar
  * 1/4 c  fish sauce* or soy sauce


Remaining Ingredients


  * 3 tb peanut oil or vegetable oil
  * 3 to 4 cloves garlic; minced or pressed
  * 1 tb fresh chile, minced or 1 1/2 ts crushed red pepper flakes
  * 2 c  carrots, grated
  * 4 lg eggs, lightly beaten with a pinch of salt
  * 2/3 c peanuts, chopped
  * 6 to 8 scallions, chopped (about 1 cup)


*Fish sauce is made from fermented salted fish.  It can be found in Asian food
stores and requires no refrigeration after opening.
"
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
I'm not kidding, nor am I misinterpreting. This is the state of your news today. They are equating being against Bush's war with treason.\r
Not agreeing with the\r
\r
govern",
  "
democratically by getting anti-war sentiments labelled as treason. Their
recommendation is to send along two witnesses for each protester in order to
protect the ability to prosecute peace-protesters for treason in the future.
Once the laws are appropriately amended.

This is the quote:

\"I believe we are one more 9/11 away from the end of the open society.

\"I really do.
\"

- Thomas Friedman

Fertig.

I'm not kidding, nor am I misinterpreting. This is the state of your news
today. They are equating being against Bush's war with treason.
Not agreeing with the

govern
"
);

$this->_munger->max_visible_output_chars = 0;
$this->_munger->force_paragraphs = false;

$this->_run_munger_test (
  "< <<Whatever this is, eh?>",
  "< <Whatever this is, eh?>"
);

$this->_run_munger_test ('<<<<<', '<<<');

$this->_run_munger_test ('<<f<<s', '<f<s');

$this->_run_munger_test ('<<<<<<f<<s<<G<<4<<<e><', '<<<f<s<G<4<<e><');

$this->_run_munger_test ('<<<f><<s', '<<f><s');


$this->_run_munger_test (
  "Horace Greeley",
  "Horace Greeley"
);

$this->_run_munger_test (
  "Line
break

Paragraph


Par/Line Break



Two Paragraphs




Two Paragraphs/Line Break





Three Paragraphs",
  "Line
break

Paragraph


Par/Line Break



Two Paragraphs




Two Paragraphs/Line Break





Three Paragraphs
"
);

$this->_run_munger_test (
  "Horace Greeley <img src=\"http://earthli.com/common/icons/webcore_png/logos/earthli_logo_full.png\" format=\"none\">Hello",
  "Horace Greeley Hello"
);

$this->_run_munger_test (
  "Horace Greeley <img src=\"http://earthli.com/common/icons/webcore_png/logos/earthli_logo_full.png\">Hello",
  "Horace Greeley [image]Hello"
);

$this->_run_munger_test (
  "Horace Greeley <img src=\"http://earthli.com/common/icons/webcore_png/logos/earthli_logo_full.png\" title=\"earthli Logo\">Hello",
  "Horace Greeley [earthli Logo]Hello"
);

$this->_run_munger_test (
  "Horace Greeley <p>This is (not) a paragraph</p>",
  "Horace Greeley <p>This is (not) a paragraph</p>"
);

$this->_run_munger_test (
  "Horace Greeley likes to write in German (üöäÖ)",
  "Horace Greeley likes to write in German (üöäÖ)"
);

$this->_run_munger_test (
  "This is a code sample: 
<pre>function DoSomething( _op: TOperation );
\tbegin
\t\texit;
\tend;
</pre>That was a code sample.",
  "This is a code sample: 

function DoSomething( _op: TOperation );
\tbegin
\t\texit;
\tend;

That was a code sample.
"
);

$this->_run_munger_test (
  "This is a code sample: <pre>function DoSomething( _op: TOperation );
\tbegin
\t\texit;
\tend;
</pre>That was a code sample.",
  "This is a code sample: 

function DoSomething( _op: TOperation );
\tbegin
\t\texit;
\tend;

That was a code sample.
"
);

$this->_run_munger_test (
  "This is a code sample: 

<pre>function DoSomething( _op: TOperation );
\tbegin
\t\texit;
\tend;

</pre>That was a code sample.",
  "This is a code sample: 

function DoSomething( _op: TOperation );
\tbegin
\t\texit;
\tend;

That was a code sample.
"
);

$this->_run_munger_test (
  "This is a code sample: 

<pre>function DoSomething( _op: TOperation );
\tbegin
\t\texit;
\tend;


</pre>That was a code sample.",
  "This is a code sample: 

function DoSomething( _op: TOperation );
\tbegin
\t\texit;
\tend;


That was a code sample.
"
);

$this->_run_munger_test (
  "This is a code sample in a box: 
<box title=\"Code Sample\"><pre>function DoSomething( _op: TOperation );
\tbegin
\t\texit;
\tend;
</pre></box>That was a code sample.",
  "This is a code sample in a box: 

---------------
| Code Sample |
---------------

function DoSomething( _op: TOperation );
\tbegin
\t\texit;
\tend;
---------------

That was a code sample.
"
);

$this->_run_munger_test (
  "This is a code sample in a box: 
<box align=\"right\" title=\"Code Sample\"><pre>function DoSomething( _op: TOperation );
\tbegin
\t\texit;
\tend;
</pre></box>That was a code sample.",
  "This is a code sample in a box: 

---------------
| Code Sample |
---------------

function DoSomething( _op: TOperation );
\tbegin
\t\texit;
\tend;
---------------

That was a code sample.
"
);

$this->_run_munger_test (
  "This is a code sample in a box: 
<box align=\"left\" title=\"Code Sample\"><pre>function DoSomething( _op: TOperation );
\tbegin
\t\texit;
\tend;
</pre></box>That was a code sample.",
  "This is a code sample in a box: 

---------------
| Code Sample |
---------------

function DoSomething( _op: TOperation );
\tbegin
\t\texit;
\tend;
---------------

That was a code sample.
"
);

$this->_run_munger_test (
  "This is a code sample in a box: 
<box align=\"center\" title=\"Code Sample\"><pre>function DoSomething( _op: TOperation );
\tbegin
\t\texit;
\tend;
</pre></box>That was a code sample.",
  "This is a code sample in a box: 

---------------
| Code Sample |
---------------

function DoSomething( _op: TOperation );
\tbegin
\t\texit;
\tend;
---------------

That was a code sample.
"
);

$this->_run_munger_test (
  "This is a code sample in a box: 
<box align=\"right\" width=\"50%\" title=\"Code Sample\"><pre>function DoSomething( _op: TOperation );
\tbegin
\t\texit;
\tend;
</pre></box>That was a code sample.",
  "This is a code sample in a box: 

---------------
| Code Sample |
---------------

function DoSomething( _op: TOperation );
\tbegin
\t\texit;
\tend;
---------------

That was a code sample.
"
);

$this->_run_munger_test (
  "This is a code sample: 
<pre>function DoSomething( _op: TOperation );
\tbegin
\t\texit;
\tend;
</pre>
That was a code sample.",
  "This is a code sample: 

function DoSomething( _op: TOperation );
\tbegin
\t\texit;
\tend;

That was a code sample.
"
);

$this->_run_munger_test (
  "This<ul><a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a></ul> here.",
  "This

  * \"is\" <something.php> my
  * first \"name\" <something.php>

 here.
"
);

$this->_run_munger_test (
  "This<ul>

<a href=\"something.php\">is</a> my
first
 <a href=\"something.php\">name</a>
</ul> here.",
  "This

  * 
  * \"is\" <something.php> my
  * first
  * \"name\" <something.php>

 here.
"
);

$this->_run_munger_test (
  "This<ul>

<a href=\"something.php\">is</a> my
<ul>first
 <a href=\"something.php\">name</a>
</ul> here.
</ul>",
  "This

  * 
  * \"is\" <something.php> my
   * first
   * \"name\" <something.php>
  * here.
"
);

$this->_run_munger_test (
  "This<ul>

<a href=\"something.php\">is</a> my
<ul>first
 <a href=\"something.php\">name</a>
</ul>
 here.
</ul>",
  "This

  * 
  * \"is\" <something.php> my
   * first
   * \"name\" <something.php>
  * here.
"
);

$this->_run_munger_test (
  "This<ul>
<a href=\"something.php\">is</a> my
first
 <a href=\"something.php\">name</a>
</ul> here.",
  "This

  * \"is\" <something.php> my
  * first
  * \"name\" <something.php>

 here.
"
);

$this->_run_munger_test (
  "This<ul>
<a href=\"something.php\">is</a> my
<ul>first
 <a href=\"something.php\">name</a>
</ul> here.
</ul>",
  "This

  * \"is\" <something.php> my
   * first
   * \"name\" <something.php>
  * here.
"
);

$this->_run_munger_test (
  "This<ul>
<a href=\"something.php\">is</a> my
<ul>first
 <a href=\"something.php\">name</a>
</ul>
 here.
</ul>",
  "This

  * \"is\" <something.php> my
   * first
   * \"name\" <something.php>
  * here.
"
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
  "   1. num1.1
   2. num1.2
      1. num2.1
      2. num2.2
   3. num1.3
"
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
  "   1. num1.1
   2. num1.2
   3. num1.3
   4. num1.4
      1. num2.1
      2. num2.2
         1. num3.1
         2. 
         3. 
         4. num3.4
            1. num4.1
            2. num4.2
      3. 
      4. num2.4
      5. 
      6. 
   5. num1.5
"
);

$this->_munger->break_inside_word = false;
$this->_munger->force_paragraphs = false;

$this->_run_munger_test (
  "<a href=\"/earthli/index.php\">home</a> <p>This is the home page.</p> Hello. I think 8 > 5 && 5 < 8.",
  "\"home\" </earthli/index.php> <p>This is the home page.</p> Hello. I think 8 > 5
&& 5 < 8.
"
);

$this->_munger->max_visible_output_chars = 6;
$this->_run_munger_test (
  "This is my name.",
  "This..."
);

$this->_munger->max_visible_output_chars = 0;
$this->_run_munger_test (
  "This is my name.",
  "This is my name."
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
  "This is my name."
);

$this->_munger->max_visible_output_chars = 17;
$this->_run_munger_test (
  "This is my name.",
  "This is my name."
);

$this->_munger->max_visible_output_chars = 18;
$this->_run_munger_test (
  "This is my name.",
  "This is my name."
);

$this->_munger->max_visible_output_chars = 19;
$this->_run_munger_test (
  "This is my name.",
  "This is my name."
);

$this->_munger->max_visible_output_chars = 0;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
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
  "This \"is\" <something.php>..."
);

$this->_munger->max_visible_output_chars = 7;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php>..."
);

$this->_munger->max_visible_output_chars = 8;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php>..."
);

$this->_munger->max_visible_output_chars = 9;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php>..."
);

$this->_munger->max_visible_output_chars = 10;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php>..."
);

$this->_munger->max_visible_output_chars = 11;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my..."
);

$this->_munger->max_visible_output_chars = 12;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my..."
);

$this->_munger->max_visible_output_chars = 13;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my..."
);

$this->_munger->max_visible_output_chars = 14;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my..."
);

$this->_munger->max_visible_output_chars = 15;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my..."
);

$this->_munger->max_visible_output_chars = 16;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my..."
);

$this->_munger->max_visible_output_chars = 17;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first ...
"
);

$this->_munger->max_visible_output_chars = 18;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php>...
"
);

$this->_munger->max_visible_output_chars = 19;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php>...
"
);

$this->_munger->max_visible_output_chars = 20;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php>...
"
);

$this->_munger->max_visible_output_chars = 21;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php>...
"
);

$this->_munger->max_visible_output_chars = 22;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php>...
"
);

$this->_munger->max_visible_output_chars = 23;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php>...
"
);

$this->_munger->max_visible_output_chars = 24;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php>...
"
);

$this->_munger->max_visible_output_chars = 25;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php>...
"
);

$this->_munger->max_visible_output_chars = 26;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php>...
"
);

$this->_munger->max_visible_output_chars = 27;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 28;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 29;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 30;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 31;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 32;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 33;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 34;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 35;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 36;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 37;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 38;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 39;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 40;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 41;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 42;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 43;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 44;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 45;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 46;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 47;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 48;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 49;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 50;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 51;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 52;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 53;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 54;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 55;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 56;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 57;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 58;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 59;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 60;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 61;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 62;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 63;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 64;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 65;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 66;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 67;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 68;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 69;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 70;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 71;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 72;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 73;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 74;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 75;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 76;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 77;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 78;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 79;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 80;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 81;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 82;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 83;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 84;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->break_inside_word = true;
$this->_munger->force_paragraphs = false;

$this->_munger->max_visible_output_chars = 0;
$this->_run_munger_test (
  "This is my name.",
  "This is my name."
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
  "This is my name..."
);

$this->_munger->max_visible_output_chars = 16;
$this->_run_munger_test (
  "This is my name.",
  "This is my name."
);

$this->_munger->max_visible_output_chars = 17;
$this->_run_munger_test (
  "This is my name.",
  "This is my name."
);

$this->_munger->max_visible_output_chars = 18;
$this->_run_munger_test (
  "This is my name.",
  "This is my name."
);

$this->_munger->max_visible_output_chars = 19;
$this->_run_munger_test (
  "This is my name.",
  "This is my name."
);

$this->_munger->max_visible_output_chars = 0;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
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
  "This \"i\" <something.php>..."
);

$this->_munger->max_visible_output_chars = 7;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php>..."
);

$this->_munger->max_visible_output_chars = 8;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> ..."
);

$this->_munger->max_visible_output_chars = 9;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> m..."
);

$this->_munger->max_visible_output_chars = 10;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my..."
);

$this->_munger->max_visible_output_chars = 11;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
...
"
);

$this->_munger->max_visible_output_chars = 12;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
f...
"
);

$this->_munger->max_visible_output_chars = 13;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
fi...
"
);

$this->_munger->max_visible_output_chars = 14;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
fir...
"
);

$this->_munger->max_visible_output_chars = 15;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
firs...
"
);

$this->_munger->max_visible_output_chars = 16;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first...
"
);

$this->_munger->max_visible_output_chars = 17;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first ...
"
);

$this->_munger->max_visible_output_chars = 18;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"n\" <something.php>...
"
);

$this->_munger->max_visible_output_chars = 19;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"na\" <something.php>...
"
);

$this->_munger->max_visible_output_chars = 20;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"nam\" <something.php>...
"
);

$this->_munger->max_visible_output_chars = 21;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php>...
"
);

$this->_munger->max_visible_output_chars = 22;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> ...
"
);

$this->_munger->max_visible_output_chars = 23;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> h...
"
);

$this->_munger->max_visible_output_chars = 24;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> he...
"
);

$this->_munger->max_visible_output_chars = 25;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> her...
"
);

$this->_munger->max_visible_output_chars = 26;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here...
"
);

$this->_munger->max_visible_output_chars = 27;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 28;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 29;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 30;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 31;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 32;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 33;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 34;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 35;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 36;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 37;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 38;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 39;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 40;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 41;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 42;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 43;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 44;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 45;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 46;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 47;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 48;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 49;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 50;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 51;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 52;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 53;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 54;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 55;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 56;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 57;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 58;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 59;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 60;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 61;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 62;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 63;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 64;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 65;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 66;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 67;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 68;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 69;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 70;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 71;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 72;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 73;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 74;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 75;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 76;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 77;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 78;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 79;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 80;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 81;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 82;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 83;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 84;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->break_inside_word = true;
$this->_munger->force_paragraphs = true;

$this->_munger->max_visible_output_chars = 0;
$this->_run_munger_test (
  "This is my name.",
  "This is my name.
"
);

$this->_munger->max_visible_output_chars = 1;
$this->_run_munger_test (
  "This is my name.",
  "T...
"
);

$this->_munger->max_visible_output_chars = 2;
$this->_run_munger_test (
  "This is my name.",
  "Th...
"
);

$this->_munger->max_visible_output_chars = 3;
$this->_run_munger_test (
  "This is my name.",
  "Thi...
"
);

$this->_munger->max_visible_output_chars = 4;
$this->_run_munger_test (
  "This is my name.",
  "This...
"
);

$this->_munger->max_visible_output_chars = 5;
$this->_run_munger_test (
  "This is my name.",
  "This ...
"
);

$this->_munger->max_visible_output_chars = 6;
$this->_run_munger_test (
  "This is my name.",
  "This i...
"
);

$this->_munger->max_visible_output_chars = 7;
$this->_run_munger_test (
  "This is my name.",
  "This is...
"
);

$this->_munger->max_visible_output_chars = 8;
$this->_run_munger_test (
  "This is my name.",
  "This is ...
"
);

$this->_munger->max_visible_output_chars = 9;
$this->_run_munger_test (
  "This is my name.",
  "This is m...
"
);

$this->_munger->max_visible_output_chars = 10;
$this->_run_munger_test (
  "This is my name.",
  "This is my...
"
);

$this->_munger->max_visible_output_chars = 11;
$this->_run_munger_test (
  "This is my name.",
  "This is my ...
"
);

$this->_munger->max_visible_output_chars = 12;
$this->_run_munger_test (
  "This is my name.",
  "This is my n...
"
);

$this->_munger->max_visible_output_chars = 13;
$this->_run_munger_test (
  "This is my name.",
  "This is my na...
"
);

$this->_munger->max_visible_output_chars = 14;
$this->_run_munger_test (
  "This is my name.",
  "This is my nam...
"
);

$this->_munger->max_visible_output_chars = 15;
$this->_run_munger_test (
  "This is my name.",
  "This is my name...
"
);

$this->_munger->max_visible_output_chars = 16;
$this->_run_munger_test (
  "This is my name.",
  "This is my name.
"
);

$this->_munger->max_visible_output_chars = 17;
$this->_run_munger_test (
  "This is my name.",
  "This is my name.
"
);

$this->_munger->max_visible_output_chars = 18;
$this->_run_munger_test (
  "This is my name.",
  "This is my name.
"
);

$this->_munger->max_visible_output_chars = 19;
$this->_run_munger_test (
  "This is my name.",
  "This is my name.
"
);

$this->_munger->max_visible_output_chars = 0;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 1;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "T...
"
);

$this->_munger->max_visible_output_chars = 2;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "Th...
"
);

$this->_munger->max_visible_output_chars = 3;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "Thi...
"
);

$this->_munger->max_visible_output_chars = 4;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This...
"
);

$this->_munger->max_visible_output_chars = 5;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This ...
"
);

$this->_munger->max_visible_output_chars = 6;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"i\" <something.php>...
"
);

$this->_munger->max_visible_output_chars = 7;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php>...
"
);

$this->_munger->max_visible_output_chars = 8;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> ...
"
);

$this->_munger->max_visible_output_chars = 9;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> m...
"
);

$this->_munger->max_visible_output_chars = 10;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my...
"
);

$this->_munger->max_visible_output_chars = 11;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
...
"
);

$this->_munger->max_visible_output_chars = 12;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
f...
"
);

$this->_munger->max_visible_output_chars = 13;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
fi...
"
);

$this->_munger->max_visible_output_chars = 14;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
fir...
"
);

$this->_munger->max_visible_output_chars = 15;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
firs...
"
);

$this->_munger->max_visible_output_chars = 16;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first...
"
);

$this->_munger->max_visible_output_chars = 17;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first ...
"
);

$this->_munger->max_visible_output_chars = 18;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"n\" <something.php>...
"
);

$this->_munger->max_visible_output_chars = 19;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"na\" <something.php>...
"
);

$this->_munger->max_visible_output_chars = 20;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"nam\" <something.php>...
"
);

$this->_munger->max_visible_output_chars = 21;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php>...
"
);

$this->_munger->max_visible_output_chars = 22;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> ...
"
);

$this->_munger->max_visible_output_chars = 23;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> h...
"
);

$this->_munger->max_visible_output_chars = 24;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> he...
"
);

$this->_munger->max_visible_output_chars = 25;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> her...
"
);

$this->_munger->max_visible_output_chars = 26;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here...
"
);

$this->_munger->max_visible_output_chars = 27;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 28;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 29;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 30;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 31;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 32;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 33;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 34;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 35;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 36;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 37;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 38;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 39;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 40;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 41;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 42;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 43;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 44;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 45;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 46;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 47;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 48;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 49;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 50;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 51;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 52;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 53;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 54;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 55;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 56;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 57;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 58;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 59;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 60;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 61;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 62;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 63;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 64;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 65;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 66;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 67;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 68;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 69;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 70;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 71;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 72;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 73;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 74;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 75;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 76;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 77;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 78;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 79;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 80;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 81;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 82;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 83;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->_munger->max_visible_output_chars = 84;
$this->_run_munger_test (
  "This <a href=\"something.php\">is</a> my
first <a href=\"something.php\">name</a> here.",
  "This \"is\" <something.php> my
first \"name\" <something.php> here.
"
);

$this->show_html_output = $old_show_html_output;

?>