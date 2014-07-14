Greek sentence segmenter
=============

A much better sentence segmeneter to use in NLTK, than the one provided, for the Greek Language.

It has been constructed on law texts (e-Themis), which is full of full stops (due to Acronyms) and other punctuation marks. Due to memory constraints, I wasn't able to feed the whole e-Themis database, but at least a big portion of it, ~70%.

To use it, open it and unpickle it. For example:

    f = open("greek.law.utf8.70.pickle")
    sent_tokenizer = pickle.load(f)
    f.close()
    sentences = sent_tokenizer.tokenize(all_my_texts)
    # sentences variable is a list of sentences


Sentence segmenter was built on the PunktSentenceTokenizer of NLTK with the following code, where "keys" are the e-Themis files.

        def create_sentence_tokenizer(files_to_read = -1, ascii = False, tok_file = 'greek.law.pickle'):
            """
                Diabazei ola ta arxeia kai dimiourgei sentence tokenizer
                H prwti parametros deixnei posa arxeia na diabasei
            """
        
            import pickle, nltk
            
            if files_to_read > len(keys):
                files_to_read = len(keys)
                print 'files_to_read trancated to %d' % files_to_read
            elif files_to_read < 0:
                files_to_read = 2 * int(len(keys)) / 3
                print 'files_to_read auto set to %d * 2/3 => %d' % (len(keys), files_to_read)
            
            print 'Reading all %d files..' % files_to_read,
            i = 0
            all_data = ""
            
            for cfile in keys:
                i += 1
                if i > files_to_read:
                    break
                
                print '[%s]' % cfile,
                all_data += my_io.read_file(txt(cfile, ascii))
                        
            print '..Done!'
            
            print 'Creating .. nltk.tokenize.punkt.PunktSentenceTokenizer()',
            tokenizer = nltk.tokenize.punkt.PunktSentenceTokenizer()
            tokenizer.train(all_data)    
            print '..Done!'
        
            print 'Dumping to hd..',
            out = open(tok_file,"wb")
            pickle.dump(tokenizer, out)
            out.close()
            print '..Done!'
            
            return tokenizer
        


Charalampos Tsimpouris
* https://www.linkedin.com/pub/charalampos-tsimpouris/5b/78a/8a
* https://www.researchgate.net/profile/Charalampos_Tsimpouris
* http://1024.gr
