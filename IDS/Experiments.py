from pyperplan.pddl.errors import ParseError
from pyperplan.pddl.lisp_parser import parse_lisp_iterator
from pyperplan.pddl.parser import *


def parseDomainDef(object_path):
    test = [
        """
  (define (domain BLOCKS)
  (:requirements :strips :typing :equality)
  (:types block)
  (:predicates (on ?x ?y - block)
	       (ontable ?x - block)
	       (clear ?x - block)
	       (handempty)
	       (holding ?x - block)
	       )

  (:action pick-up
	     :parameters (?x - block)
	     :precondition (and (clear ?x) (ontable ?x) (handempty))
	     :effect
	     (and (not (ontable ?x))
		   (not (clear ?x))
		   (not (handempty))
		   (holding ?x)))

  (:action put-down
	     :parameters (?x - block)
	     :precondition (holding ?x)
	     :effect
	     (and (not (holding ?x))
		   (clear ?x)
		   (handempty)
		   (ontable ?x)))
  (:action stack
	     :parameters (?x ?y - block)
	     :precondition (and (holding ?x) (clear ?y) (not (= ?x ?y)))
	     :effect
	     (and (not (holding ?x))
		   (not (clear ?y))
		   (clear ?x)
		   (handempty)
		   (on ?x ?y)))
  (:action unstack
	     :parameters (?x ?y - block)
	     :precondition (and (on ?x ?y) (clear ?x) (handempty) (not (= ?x ?y)))
	     :effect
	     (and (holding ?x)
		   (clear ?y)
		   (not (clear ?x))
		   (not (handempty))
		   (not (on ?x ?y)))))
    """
    ]

    print("\n \n")
    iter = parse_lisp_iterator(test)
    dom = parse_domain_def(iter)
    print(dom.name)
    for key in dom.requirements.keywords:
        print(key.name)
    print("\n")

    # use parser.py for class documentation, not pddl.py

    #making a variable for Type Constructor
    seq_num = Type("seq-num",None)

    #adding to types list
    dom.types.append(seq_num)
    for t in dom.types:
        print(t.name)

    #adding to constants lists  
    dom.constants = [Object("0",seq_num.name)]
    if dom.constants != None:
        for e in dom.constants:
            print(e.name, e.typeName)

    #adding to predicates (fluents List)
    dom.predicates.predicates.append(Predicate("observed",[Variable("?n",[seq_num.name])]))

    obs_dat = open(object_path,"r")

    obs_data = obs_dat.readlines()

    
    for e in range(1, len(obs_data) + 1):
        dom.constants.append(Object(e, seq_num.name))
        line = obs_data[e-1]
        val_brack = line.find("(")
        val_space = line.find(" ")
        val_action = line[val_brack + 1,val_space]

        action = None
        for i in dom.actions:
            if (i.name.lower() == val_action.lower()):
                action = i
                break

        if (action == None):
            print("ERROR non ACTION FOUND for ", val_action)
        
        action_parameter_list = []
        action_precondition_list = []
        action_effect_list = []

        # may need to make recursive just in case it doesnt work
        for i in action.parameters:
            action_parameter_list.append(Variable(i.name, i.types))
        for i in action.precond:
            action_precondition_list.append(Formula(i.key, i.children, i.type))
        for i in action.effect:
            action_effect_list.append(Formula(i.key, i.children, i.type))

        deep_action = ActionStmt(action.name, action_parameter_list, action_precondition_list, action_effect_list)

    pred = dom.predicates
    print("\n")
    for p in pred.predicates:
        print(p.name, str(p))
    print("\n")
    for p in pred.predicates:
        if p.parameters !=[]:
            print(p.parameters[0].name, p.parameters[0].types[0])
        # 
        if len(p.parameters) > 1:
            print(p.parameters[1].types[0])
    print("\n")
    print("number of actions is ",len(dom.actions))
    action = dom.actions[3]
    print("\n")
    print ("for third action",action.name) 
    print (action.parameters[0].name) 
    print (action.parameters[0].types[0])
    print (action.parameters[1].name)
    print (action.parameters[1].types[0])
    pre = action.precond.formula
    print ("precondition key",pre.key)
    for c in pre.children:
        print (c.key)
    print("\n")
    print(pre.children[0].children[0].key.name)
    print(pre.children[0].children[1].key.name)
    print(pre.children[1].children[0].key.name)
    print(pre.children[2].children)
    print("\n")
    eff = action.effect.formula
    print (eff.key)
    for c in eff.children:
        print(c.key)

    pretty = open("pretty_pddl.pddl","w")

    pretty.write("(define (domain {})".format(dom.name))

    pretty.close()

def parseObjectsStmt():
    test = [
        """(:objects
      D R A W O E P C - block
    )"""
    ]
    iter = parse_lisp_iterator(test)
    objects = parse_objects_stmt(iter)
    for o in objects:
        print (o.name)

def parseInitStmt():
    test = [
        """
    (:INIT (CLEAR C) (CLEAR A H) (CLEAR B) (CLEAR D) (ONTABLE C) (ONTABLE A)
     (ONTABLE B) (ONTABLE D) (HANDEMPTY))"""
    ]
    iter = parse_lisp_iterator(test)
    init = parse_init_stmt(iter)
    for p in init.predicates:
        print (p.name, p.parameters)

def parseGoalStmt():
    test = ["""(:goal (AND (ON D C) (ON C B) (ON B A)))"""]
    iter = parse_lisp_iterator(test)
    goal = parse_goal_stmt(iter)
    f = goal.formula
    print (f.key, "\n")
    for c in f.children:
        print (c.key)
    for c2 in [c1.children for c1 in f.children]:
        print(c2[0].key, c2[1].key)
