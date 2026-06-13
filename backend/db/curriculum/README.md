# Curriculum Development

## Introduction

### Using the Logistic Model to Approximate Skill Acquisition

The standard logistic function is:

$$S(t) = \frac{L}{1+e^{-k(t-t_0)}}$$

Where *t* represents training time (approximated by belt rank), *k* represents the learning curve, and *L* represents the maximum value for a learned skill.

In the interests of simplicity, *k* = 1 and *L* = 5, although as we collect data, better approximations can be made.

For skill assessment, instructors shall score on a scale from 0 to 5, where `null` indicates the skill has not been assessed, 0 indicates the skill has been evaluated but not yet acquired,

| Score  | Assessment              |
| ------ | ----------------------- |
| `null` | Not assessed            |
| 0      | Cannot describe/perform |
| 1      | Rudimentary level       |
| 2      | Beginner level          |
| 3      | Intermediate level      |
| 4      | High level              |
| 5      | Very high level         |

## Skill Data Structure

    {
        "uuid" : <string>,
        "name" : <string>,
        "description" : <string>,    
        "difficulty" : <int, default: 0>,    
        "comprises" : [ <uuid>, ... ]
    }
