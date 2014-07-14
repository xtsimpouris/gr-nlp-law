# -*- coding: utf-8 -*-
"""
Created on Mon Jan 17 10:20:19 2011

@author: xaris
"""

import os, pickle, nltk
import utils.my_io as my_io
import utils.my_text as my_text

cur_path = os.path.abspath(__file__) 
cur_path = cur_path.replace('\\', '/') # For both windows and linux
cur_path = cur_path[ : len(cur_path) - cur_path[::-1].find('/') ]
doc_path = cur_path + "doc-archive/"

txt_path_utf8 = cur_path + "txt-archive-utf8/"
txt_path_ascii = cur_path + "txt-archive-ascii/"

txt_path = txt_path_utf8

temp = my_io.get_files(".txt", txt_path, full_path = False)
keys = []
for t in temp: 
    keys.append( t.replace(".txt", "") )
keys.sort()

# Get chapters keys
temp = my_io.get_files(".txt", txt_path + 'tokenized_chapters/', full_path = False)
chapter_keys = []
for t in temp: 
    chapter_keys.append( t.replace(".txt", "") )
chapter_keys.sort()
temp = None

def get_class(a):
    b = my_io.base_name(a)
    return b[0:2] + b[b.find("_"):].replace('.txt', '')

keys_per_class = {}
for k in keys:
    if not get_class(k) in keys_per_class:
        keys_per_class[ get_class(k) ] = []
    keys_per_class[ get_class(k) ].append(k)

chapter_keys_per_class = {}
for k in chapter_keys:
    if not get_class(k) in chapter_keys_per_class:
        chapter_keys_per_class[ get_class(k) ] = []
    chapter_keys_per_class[ get_class(k) ].append(k)

def txt(a, ascii = False):
    """TXT arxeio keimenou"""
    if ascii:
        return txt_path_ascii + a + ".txt"
    else:
        return txt_path_utf8 + a + ".txt"

def tokenized_sent(a, ascii = False):
    """TXT arxeio keimenou opou einai apothikebmeno xwrismeno se keimeno"""
    if ascii:
        return txt_path_ascii + 'tokenized_sent/' + a + ".txt"
    else:
        return txt_path_utf8 + 'tokenized_sent/' + a + ".txt"

def tokenized_word(a, ascii = False):
    """TXT arxeio keimenou opou einai apothikebmeno xwrismeno se lekseis"""
    if ascii:
        return txt_path_ascii + 'tokenized_word/' + a + ".txt"
    else:
        return txt_path_utf8 + 'tokenized_word/' + a + ".txt"

def tokenized_chapter(a, ascii = False):
    """TXT arxeio keimenou opou einai apothikebmeno xwrismeno se chapters"""
    if ascii:
        return txt_path_ascii + 'tokenized_chapters/' + a + ".txt"
    else:
        return txt_path_utf8 + 'tokenized_chapters/' + a + ".txt"

def doc(a):
    """Word Arxeio keimenou"""
    return doc_path + a + ".doc"

def corpus(a):
    """Keimeno, me XML episimeiwsi"""
    return cur_path + a + ".corpus"

def acronyms(a):
    """Lista akronimiwn"""
    return cur_path + a + ".acronyms"

def manual(a):
    """Arxeia me oles tis emfaniseis akrwnimiwn"""
    return cur_path + a + ".manual"

def temp(a):
    """Arxeio Dokimwn"""
    return cur_path + a + ".temp.txt"

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
    

def get_statistics(_print = True, ascii = True):
    """
        Diabazei ola ta arxeia kai bgazei kapoia basika statistika
        arityhmos akrwnimiwn (simfwna me sigkekrimeni regex)
        paradeigmata akronimiwn
        plithos tokens
        
        Doulebei kalitera me ascii arxeia
    """
    import nltk, re
    
    tokenizer = nltk.WhitespaceTokenizer() # ~32.000.000
    #tokenizer = nltk.tokenize.RegexpTokenizer(r'\w+|[^\w\s]+') # ~47.000.000
    
    pattern = r''
    # pattern += r'[Α-Ωα-ωέύίόάήώΈΎΊΌΆΉΏ]+-(\n)[Α-Ωα-ωέύίόάήώΈΎΊΌΆΉΏ]+'
    pattern += r'[Α-Ω][Α-Ω\.]{2, 20}'
    pattern += r'|\$?\d+(\.\d+)?%?'
    pattern += r'|\.\.[\.]+'
    pattern += r'|\w+'
    pattern += r'|[][.,;"\'?():-_`]'

    #tokenizer = nltk.tokenize.RegexpTokenizer(pattern)
    
    #m = re.findall(u"((([Α-Ω]{1,4}\.) ?)+)([^Α-Ω][^.][^ ])", t1) #180
    #m = re.findall(u"( (([Α-Ω]{1,4}\.) ?)([^Α-Ω][^.][^ ]) | (([Α-Ω]{1,4}))([^Α-Ω][^.][^ ]) )+", t1) #256
    #m = re.findall(u"(([Α-Ω \-]{1,20}\.?)+)", t1) #1548
    #m = re.findall(u"(([Α-Ω]\.)([0-9Α-Ωα-ω \-]\.?){1,20})", t1) #1737
    #m = re.findall(u"[^Α-Ω](([Α-Ω.]{1,4} ?){1,9})", t1)
    
    words = {}
    all_words = 0
    acr_ret = {}
    all_acr_per_file = {}
    all_acr = {}
    examples_ret = {}
    i = 0
    
    print 'Reading all %d files..' % len(keys),
    for cfile in keys:
        print '[%s]' % cfile,
        all_data = my_io.read_file(txt(cfile, ascii))
        tokenized = tokenizer.tokenize(all_data)
        # tokenized = re.findall(pattern, all_data)
        print len(tokenized),
        
        my_io.write_file(tokenized_word(cfile, ascii), '\n*\n'.join(tokenized))
        
        words[ cfile ] = len(tokenized)
        all_words = all_words + words[ cfile ]
        
        acr_ret[ cfile ] = {}
        examples_ret[ cfile ] = []
        m = re.findall("((([ΑΒΓΔΕΖΗΘΙΚΛΜΝΞΟΠΡΣΤΥΦΧΨΩ]{1,4}\.) ?)+)([^Α-Ω][^.][^ ])", all_data)
        for mm in m:
            """
            for k in mm:
                print k,
            print ''
            """
            
            k = mm[0].strip()
            
            if not k in acr_ret[ cfile ]:
                acr_ret[ cfile ][ k ] = 'yes'
                all_acr[ k ] = 'yes'
                
                if not k in all_acr_per_file and len(examples_ret[ cfile ]) < 5:
                    examples_ret[ cfile ].append( k )
                    all_acr_per_file[ k ] = 'yes'
        i += 1
        #if i > 5:
        #    break
                
    print '..Done!'
    
    if (_print):
        print "%10d\t%s" % (all_words, 'Ola ta arxeia')
        print "%10d\t%s" % (len(all_acr), 'All unicke acronyms')
        print "%s\t%s\t%s\t%s" % ('Words', 'Unicqe acronyms', 'Examples', 'File')
        for cfile in keys:
            print "%10d\t%4d\t%s\t%s" % (words[ cfile ], len(acr_ret[ cfile ]), ', '.join(examples_ret[ cfile ]), cfile)
    
    return words

def tokenize_words(from_folder, to_folder, sent_tok_file = 'greek.law.ascii.70.pickle', encoding = "ISO-8859-7", chapters_files = True):
    """
        Diabazei ola ta arxeia kai ta xwrizei se protaseis
        .. kai meta ta xwrizei se tokens me kapoion nltk tokenizer
        
        Doulebei kalitera me ascii arxeia
        
        Gia utf -> greek.law.utf8.70.pickle / utf-8
        An den einai fakelos me chapters -> chapters_files = False
    """
    import nltk, pickle, time
    
    now = time.time()
    from_folder = from_folder.replace("{ET}", cur_path)    
    to_folder = to_folder.replace("{ET}", cur_path)    
    
    print "Loading word tokenizer...",
    word_tokenizer = nltk.WhitespaceTokenizer()
    print "..Done"
    
    print 'Loading sentence tokenizer pickled file..'
    f = open(sent_tok_file)
    sent_tokenizer = pickle.load(f)
    f.close()
    print '..Done!'

    sentences_per_file = {}
    sentences_per_category = {}
    tokens_per_file = {}
    tokens_per_category = {}  
    
    all_files = my_io.get_files(".txt", from_folder, full_path = False)

    print 'Reading all %d files..' % len(all_files),
    for cfile in all_files:
        if chapters_files:
            category = cfile[0:3] + cfile[7:].replace('.txt', '')
        else:
            category = cfile.replace('.txt', '')
        print '[%s]' % category,
        
        all_data = my_io.read_file(from_folder + cfile, encoding)
        tokenized_sentences = sent_tokenizer.tokenize(all_data)
        sentences_per_category[ category ] = sentences_per_category.get(category, 0) + len(tokenized_sentences)
        sentences_per_file[ cfile ] = len(tokenized_sentences)
        
        all_tokens = []
        for sentence in tokenized_sentences:
            all_new_tokens = word_tokenizer.tokenize(sentence)
            all_tokens += all_new_tokens
            
        tokens_per_category[ category ] = tokens_per_category.get(category, 0) + len(all_tokens)
        tokens_per_file[ cfile ] = len(all_tokens)
        my_io.write_file(to_folder + cfile, '\n*\n'.join(all_tokens), encoding)
               
    print '..Done!'
    later = time.time()
    difference = later - now

    print 'Writing to log ' + to_folder + "log.txt" + '..',
    f = open(to_folder + "log.txt", "w")
    f.write("Execution parameters\n---------------------------------------------------------\n")
    f.write("       From folder: %s\n" % from_folder)
    f.write("         To folder: %s\n" % to_folder)
    f.write("Sentence tokenizer: %s\n" % sent_tok_file)
    f.write("    Word tokenizer: %s\n" % str(word_tokenizer))
    f.write("          Encoding: %s\n" % encoding)
    f.write("     Chapter files: %s\n" % str(chapters_files))
    f.write("    Execution time: %d secs\n" % int(difference))
    f.write("---------------------------------------------------------\n")
    
    if chapters_files:    
        f.write("Results per Chapter\n---------------------------------------------------------\n")
        f.write("Tokens\tSentences\tChapter\n")
        all_t = 0
        all_s = 0
        for cfile in sentences_per_category:
            all_t += tokens_per_category[ cfile ]
            all_s += sentences_per_category[ cfile ]
            f.write("%10d\t%6d\t%s\n" % (tokens_per_category[ cfile ], sentences_per_category[ cfile ], cfile))
        f.write("%10d\t%6d\t%s\n" % (all_t, all_s, "Sum"))
        f.write("---------------------------------------------------------\n")
    
    f.write("Results per file\n---------------------------------------------------------\n")
    f.write("Tokens\tSentences\tFile\n")
    all_t = 0
    all_s = 0
    for cfile in sentences_per_file:
        all_t += tokens_per_file[ cfile ]
        all_s += sentences_per_file[ cfile ]
        f.write("%10d\t%6d\t%s\n" % (tokens_per_file[ cfile ], sentences_per_file[ cfile ], cfile))
    f.write("%10d\t%6d\t%s\n" % (all_t, all_s, "Sum"))
    f.write("---------------------------------------------------------\n")

    f.close()
    print '..Done!'

def cut_in_chapters(threshold = 30000):
    """
        Kobei ola ta arxeia se kefalaia, kai diorthwnei tin arithmisi 1, 1a, 1b ...
        to threshold omadopei kefalaia/kommatia
    """
    i = 0
    last_numbering = ''
    category = ''
    # cutter = u"ΚΕΦΑΛΑΙΟ"
    cutter = u"Σελ. "   # giati ta kefalaia dinoun terastia arxeia!
    
    print 'Reading all %d files..' % len(keys),
    for cfile in keys:     
        numbering = "%02d" % int(cfile[0:2])
        if (numbering != last_numbering):
            last_numbering = numbering
            category = cfile[cfile.find('_') + 1:]
            chapter = 0
            print
            print numbering, category,
            sa = u""
        
        all_data = my_io.read_file(txt(cfile, False))
        chapters = all_data.split(cutter) 
        
        for c in range(len(chapters)):
            chapters[c] = chapters[c].replace("\r", "")
            chapters[c] = chapters[c].replace("\n  \n", "\n")
            chapters[c] = chapters[c].replace("\n\n\n", "\n")
            chapters[c] = chapters[c].replace("\n\n", "\n")
            chapters[c] = chapters[c].replace("\n\n", "\n")
            chapters[c] = chapters[c].replace(u"’ρθρο", u"Άρθρο")
            
            if c == 0:
                # first one is only titles..
                sa = sa + chapters[0]
                continue
            
            sa = sa + cutter + chapters[c]
            if len(sa) > threshold or (c == len(chapters) - 1 and len(sa) > threshold / 2):
                # c == len(chapters) - 1 and len(sa) > threshold / 2 => avoid to leave somethinf behind
                chapter += 1
                # print chapter,
                fname = "%s|%03d_%s" % (numbering, chapter, category)
                my_io.write_file(tokenized_chapter(fname, False), sa)
                sa = u""
                
        i += 1
        #if i > 5:
        #    break
                
    print '..Done!'


def get_chapters_statistics(sent_tok_file = "greek.law.utf8.70.pickle"):
    """
        Diabazei ola ta arxeia pou einai ta kefalaia kai bgazei statistika
        plithos kefalaiwn(arxeiwn) ana katigoria
    """
    f = open(sent_tok_file)
    sent_tokenizer = pickle.load(f)
    f.close()

    # Arxikopoiisi word tokenizer
    word_tokenizer = nltk.WhitespaceTokenizer()
    
    print "%s\t%s" % ("eThemis class", "Onoma klassis")
    print "%s\t%s" % ("#doc", "Plithos keimenwn stin sigkekrimeni katigoria")
    print "%s\t%s" % ("#tok", "Plithos tokens stin sigkekrimeni katigoria [kommena me nltk.WhitespaceTokenizer()]")
    print "%s\t%s" % ("#sent", "Plithos protasewn sti sigkekrimeni katigoria [kommena me %s]" % sent_tok_file)
    print "%s\t%s" % ("#stem", "Plithos stemmed tokens")
    print "%s\t%s" % ("#gr_stem", "To idio me #stem [adiaforo]")
    print "%s\t%s" % ("#tok/sent", "Plithos tokens ana protasi")
    print "%s\t%s" % ("#sent/doc", "Plithos protasewn ana keimeno")
    
    print "%s\t%s\t%s\t%s\t%s\t%s\t%s\t%s" % ('eThemis class', '#doc', '#tok', '#sent', '#stem', '#gr_stem', '#tok/sent', '#sent/doc')
    ckeys = chapter_keys_per_class.keys()
    ckeys.sort()
    for eclass in ckeys:
        num_tokens = 0
        num_sentences = 0
        num_stemm_tokens = 0
        num_grek_stemm_tokens = 0
        
        for cfile in chapter_keys_per_class[eclass]:
            all_data = my_io.read_file(tokenized_chapter(cfile))
            all_sentences = sent_tokenizer.tokenize(all_data)
            
            num_sentences += len(all_sentences)
            for s in all_sentences:
                all_tokens = word_tokenizer.tokenize(s)
                num_tokens += len(all_tokens)
            
            stemms = my_text.get_stemmed_file(tokenized_chapter(cfile), sent_tok_file, grek = False, quiet = True)
            num_stemm_tokens += len(stemms)
            # stemms = my_text.get_stemmed_file(tokenized_chapter(cfile), sent_tok_file, grek = True, quiet = True)
            num_grek_stemm_tokens += len(stemms)
        
        print "%s\t%d\t%d\t%d\t%d\t%d\t%10.2f\t%15.2f" % (eclass, len(chapter_keys_per_class[eclass]), num_tokens, num_sentences, num_stemm_tokens, num_grek_stemm_tokens, num_tokens / float(num_sentences), num_sentences / float(len(chapter_keys_per_class[eclass])))

def tokenize_sentences(ascii = False, tok_file = 'greek.law.pickle', _print = True):
    """
        Diabazei ola ta arxeia kai bgazei kapoia basika statistika
        plithos sentences
    """

    import pickle
    
    print 'Reading tokenizer pickled file..'
    f = open(tok_file)
    tokenizer = pickle.load(f)
    f.close()
    print '..Done!'
    sentences = {}
    all_sentences = 0
    i = 0
    
    print 'Reading all %d files..' % len(keys),
    for cfile in keys:
        print '[%s]' % cfile,
        all_data = my_io.read_file(txt(cfile, ascii))
        tokenized = tokenizer.tokenize(all_data)
        sentences[ cfile ] = len(tokenized)
        all_sentences = all_sentences + sentences[ cfile ]
        
        my_io.write_file(tokenized_sent(cfile, ascii), '\n*\n'.join(tokenized))
        
        i += 1
                
    print '..Done!'
    
    if (_print):
        print "%10d\t%s" % (all_sentences, 'Ola ta arxeia')
        print "%s\t%s" % ('Sentences', 'File')
        for cfile in keys:
            print "%10d\t%s" % (sentences[ cfile ], cfile)
    
    return sentences
