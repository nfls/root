import urllib
for i in range(1, 613):
	urllib.urlretrieve("http://identicon.relucks.org/" + str(i) + "?size=200", "../public/avatar/" + str(i)+".png")
