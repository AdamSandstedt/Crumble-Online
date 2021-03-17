// This class is only used internally by the algorithm (in the recursive call)
class EvaluationAndMove {
  constructor(move, evaluation) {
    this.move = move;
    this.evaluation = evaluation;
  }
}

function minmax(gameState, gameRules, evaluate, plies, alpha = Number.NEGATIVE_INFINITY, beta = Number.POSITIVE_INFINITY, statisticsHook) {
  function _minmax(gameState, pliesRemaining, alpha, beta, maximizing) {
    if (statisticsHook!=null) statisticsHook.visitedNode(gameState);
    const vForTerminal = gameRules.terminalStateEval(gameState);
    if ( (vForTerminal!=null) || (pliesRemaining===0)) {
      if (statisticsHook!=null) statisticsHook.evaluatedLeafNode(gameState);
      const v2 = (vForTerminal!=null?vForTerminal:evaluate(gameState));
      return new EvaluationAndMove(null, v2*(maximizing?1:-1));
    } else {
      // construct the children and evaluate them
      const moves = gameRules.listMoves(gameState);
      const NUM_OF_MOVES = moves.length;
      if (NUM_OF_MOVES<=0)
      throw `weird number of moves (${NUM_OF_MOVES}) in non-terminal state`
      // one can add cleverness and squeeze the two branches into one at the expense of readability
      if (maximizing) {
        var v = Number.NEGATIVE_INFINITY;
        var bestMove = null;
        for (let i = 0; i < NUM_OF_MOVES ; i++) {
          const nextState = gameRules.nextState(gameState, moves[i]);
          const nextStateEval = _minmax(nextState, pliesRemaining-1, Math.max(v, alpha), beta, !maximizing);
          if (nextStateEval!=null) {
            if (nextStateEval.evaluation > v) {
              if (nextStateEval.evaluation===Number.POSITIVE_INFINITY) // no need to look any further
              return new EvaluationAndMove(moves[i], nextStateEval.evaluation);
              v        = nextStateEval.evaluation
              bestMove = moves[i];
            }
          } else throw new Error('impossible at this point');
          if ((v>=beta) && (i!==NUM_OF_MOVES-1)) { /* sse-1512513725: in the various resources on the algorithm I
            always see this as (v>beta) but I am confident there is no
            reason not to use ">=" instead as this is better (it
            increases the likelihood of pruning). Also, if this is
            the last child, we don't consider it a true pruning incident
            for statistical purposes (the logic remains effectively the
            same as for the last child we are going to break out of the
            loop anyways */
            if (statisticsHook!=null) statisticsHook.pruningIncident(gameState, true, v, beta, i);
            break;
          }
        }
        if (! ((v===Number.NEGATIVE_INFINITY) || (bestMove!=null) ))
        throw `maximizing node, v is ${v==null?'null':v}, bestMove is: ${bestMove==null?'null':bestMove} - this makes no sense`;
        return new EvaluationAndMove(bestMove!==null?bestMove:moves[0], v); // if all moves are equally bad, return the first one
      } else {
        var v = Number.POSITIVE_INFINITY;
        var bestMove = null;
        for (let i = 0; i < NUM_OF_MOVES ; i++) {
          const nextState = gameRules.nextState(gameState, moves[i]);
          const nextStateEval = _minmax(nextState, pliesRemaining-1, alpha, Math.min(v,beta), !maximizing);
          if (nextStateEval!=null) {
            if (nextStateEval.evaluation===Number.NEGATIVE_INFINITY) // no need to look any further
            return new EvaluationAndMove(moves[i], nextStateEval.evaluation);
            if (nextStateEval.evaluation<v) {
              v        = nextStateEval.evaluation;
              bestMove = moves[i];
            }
          } else throw new Error('impossible at this point');
          if ((v<=alpha) && (i!==NUM_OF_MOVES-1)) { // see sse-1512513725 (mutatis mutandis)
            if (statisticsHook!=null) statisticsHook.pruningIncident(gameState, false, v, alpha, i);
            break;
          }
        }
        if (! ((v===Number.POSITIVE_INFINITY) || (bestMove!=null)))
        throw `minimizing node, v is ${v==null?'null':v}, bestMove is: ${bestMove==null?'null':bestMove} - this makes no sense`;
        return new EvaluationAndMove(bestMove!==null?bestMove:moves[0], v); // if all moves are equally bad, return the first one
      }
    }
  }
  const v = gameRules.terminalStateEval(gameState);
  if (v!=null)
  return {
    bestMove: null,
    evaluation: v
  };
  else {
    if (! (Number.isInteger(plies) && (plies>=0) ))
    throw `illegal plies for minmax: ${plies}`;
    const evalAndMove = _minmax(gameState, plies, alpha, beta, true); // in the min-max algorithm the player who is to make the move is the maximizing player
    if (! ( (plies===0) || (evalAndMove.move!=null) ))
    throw `this is not a terminal state, plies were not 0 (they were ${plies}) and yet, no move was found, this makes no sense`;
    return {
      bestMove  : evalAndMove.move,
      evaluation: evalAndMove.evaluation
    };

  }
}
