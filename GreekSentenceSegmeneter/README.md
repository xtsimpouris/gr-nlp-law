Greek sentence segmenter
=============

A much better sentence segmeneter to use in NLTK, than the one provided, for the Greek Language.

It has been evaluated on law texts, which is full of full stops and other punctuation marks

To use it, open it and unpickle it. For example:
<code>
  f = open("greek.law.utf8.70.pickle")
  sent_tokenizer = pickle.load(f)
  f.close()

  sentences = sent_tokenizer.tokenize(all_my_texts)
  # sentences variable is a list of sentences
</code>

Charalampos Tsimpouris
* https://www.linkedin.com/pub/charalampos-tsimpouris/5b/78a/8a
* https://www.researchgate.net/profile/Charalampos_Tsimpouris
* http://1024.gr
