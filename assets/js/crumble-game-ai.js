class CPoint {
  constructor(x, y) {
    this.x = x;
    this.y = y;
  }

  // Returns an interned CPoint with the given arguments
  // For a non-interned CPoint, use the constructor
  // The coordinates are in terms of the game board i.e.
  // (0,0) = bottom left and (6,6) = top right if the board is 6x6
  // Arguments: x, y
  static make(x, y) {
    var str = x + "," + y;
    var cp = this.cpoints[str];
    if(cp == null) {
      cp = new CPoint(x, y);
      this.cpoints[str] = cp;
    }
    return cp;
  }

  toString() {
    return this.x + "," + this.y;
  }

  // Converts the coordinates so that they match the canvas coordinates for drawing
  // Arguments: canvas, hoard height, board width
  toPoint(canvas, bh, bw) {
    var p = {};
    p.x = canvas.width * this.x / bw;
    p.y = canvas.height * (bh - (this.y)) / bh;
    return p;
  }
}
CPoint.cpoints = {};

class CPiece {
  constructor(bl, tr, color) {
    this.bl = bl;
    this.tr = tr;
    this.color = color;
  }

  // Returns an interned CPiece with the given arguments
  // For a non-interned CPiece, use the constructor
  // Arguments: bottom left piece, top right piece, color
  static make(bl, tr, color) {
    var str = bl.toString() + ";" + tr.toString() + ";" + color;
    var cp = this.cpieces[str];
    if(cp == null) {
      cp = new CPiece(bl, tr, color);
      this.cpieces[str] = cp;
    }
    return cp;
  }

  // Draws the piece and an outline of the opposite color around it
  // Arguments: canvas to draw on, board height, board width
  draw(canvas, bh, bw, strokeColor, notation) {
    var p1 = this.bl.toPoint(canvas, bh, bw);
    var p2 = this.tr.toPoint(canvas, bh, bw);
    const context = canvas.getContext('2d');
    if(!strokeColor) {
      context.fillStyle = this.color == 'b' ? "black" : "white";
      context.fillRect(p1.x, p2.y, p2.x-p1.x, p1.y-p2.y);
      context.strokeStyle = this.color == 'b' ? "white" : "black";
    } else {
      context.lineWidth = 4;
      context.strokeStyle = strokeColor;
    }
    context.strokeRect(p1.x, p2.y, p2.x-p1.x, p1.y-p2.y);
    if(notation) {
      var fontSize = 64 * (this.tr.x - this.bl.x) / Math.sqrt(notation.length);
      context.font = fontSize + "px Arial";
      context.fillStyle = this.color == 'b' ? "white" : "black";
      context.fillText(notation, p1.x+5, p1.y-5);
    }
  }

  // Returns true if the pieces are the same color and share a side and therefore are able to form a chain
  // Arguments: CPiece to check against the "this" object
  isNeighbor(cp) {
    return this.color == cp.color &&
         ((this.bl.x == cp.tr.x || this.tr.x == cp.bl.x) && this.bl.y < cp.tr.y && this.tr.y > cp.bl.y ||
          (this.bl.y == cp.tr.y || this.tr.y == cp.bl.y) && this.bl.x < cp.tr.x && this.tr.x > cp.bl.x);
  }

  // Returns true if the pieces are the same color and share a corner but not a side
  // (can't form a chain but can block a capture)
  // Arguments: CPiece to check against the "this" object
  isCorner(cp) {
    return this.color == cp.color &&
          (this.bl.x == cp.tr.x || this.tr.x == cp.bl.x) &&
          (this.bl.y == cp.tr.y || this.tr.y == cp.bl.y);
  }
}
CPiece.cpieces = {};

class CBoard {
  constructor(height, width, extra = 'n') {
    if(height) {
      this.height = height;
      this.width = width;
      this.extra = extra;
      this.reset();
    }
  }

  reset() {
    this.blMap = new Map();
    this.trMap = new Map();
    var offset = this.extra == "b" ? 1 : 0;
    for(var i = 0; i < this.height; i++) {
      for(var j = 0; j < this.width; j++) {
        var cp = CPiece.make(CPoint.make(j, i), CPoint.make(j+1, i+1), (i+j+offset) % 2 == 0 ? 'w' : 'b');
        this.blMap.set(cp.bl, cp);
        this.trMap.set(cp.tr, cp);
      }
    }
  }

  loadFromBoard(cb) {
    this.height = cb.height;
    this.width = cb.width;
    this.extra = cb.extra;
    this.blMap = new Map(cb.blMap);
    this.trMap = new Map(cb.trMap);
  }

  // Draws all of the board pieces
  // Arguments: canvas to draw on
  draw(canvas, notations, showNotations) {
    for(let cp of this.blMap.values()) {
      if(!notations.get(cp) || !showNotations)
        cp.draw(canvas, this.height, this.width);
      else
        cp.draw(canvas, this.height, this.width, undefined, notations.get(cp).toString());
    }
  }
}

class CNotation {
  constructor(moves) {
    this.moves = moves;
  }

  right(between) {
    var newMoves = this.moves.slice();
    if(newMoves.length % 2 == 0) {
      newMoves.push(between);
    } else {
      newMoves[newMoves.length-1] += between;
    }
    return new CNotation(newMoves);
  }

  up(between) {
    var newMoves = this.moves.slice();
    if(newMoves.length % 2 == 0) {
      newMoves[newMoves.length-1] += between;
    } else {
      newMoves.push(between);
    }
    return new CNotation(newMoves);
  }

  toString() {
    var str = this.moves[0].toString();
    for(var i = 1; i < this.moves.length; i++) {
      str += "," + this.moves[i];
    }
    return str;
  }
}

class CGame {
  constructor(cboard, autoWin, time, timeb, timew) {
    if(cboard) {
      this.board = cboard;
      this.autoWin = autoWin;
      this.time = time;
      this.timeb = timeb;
      this.timew = timew;
      this.originalAction = "";
      this.reset();
    }
  }

  reset() {
    this.turn = 'b';
    this.hSplits = {b: new Map(), w: new Map()};
    this.vSplits = {b: new Map(), w: new Map()};
    this.joins = {b: new Map(), w: new Map()};
    this.neighbors = new Map();
    this.corners = new Map();
    this.chains = new Set();
    this.notationMap = new Map();
    this.action = "";
    this.notation = "";
    this.winner = undefined;
    this.history = [];
    this.historyIndex = 0;
    for(let cp of this.board.blMap.values()) {
      this.neighbors.set(cp, new Set());
      this.corners.set(cp, new Set());
      for(let dx of [-1, 1]) {
        for(let dy of [-1, 1]) {
          var x = cp.bl.x + dx;
          var y = cp.bl.y + dy;
          if(x < 0 || x > this.board.width - 1 || y < 0 || y > this.board.height - 1)
            continue;
          this.corners.get(cp).add(this.board.blMap.get(CPoint.make(x, y)));
        }
      }
      this.chains.add(new Set([cp]));
    }
    this.updateSplitsJoins();
    this.updateNotations();
  }

  loadFromGame(cg) {
    this.board = new CBoard();
    this.board.loadFromBoard(cg.board);
    this.autoWin = cg.autoWin;
    this.time = cg.time;
    this.timeb = cg.timeb;
    this.timew = cg.timew;
    this.originalAction = "";
    this.turn = cg.turn;
    this.hSplits = {b: new Map(), w: new Map()};
    for(let key of cg.hSplits["b"].keys())
      this.hSplits["b"].set(key, cg.hSplits["b"].get(key).slice());
    for(let key of cg.hSplits["w"].keys())
      this.hSplits["w"].set(key, cg.hSplits["w"].get(key).slice());
    this.vSplits = {b: new Map(), w: new Map()};
    for(let key of cg.vSplits["b"].keys())
      this.vSplits["b"].set(key, cg.vSplits["b"].get(key).slice());
    for(let key of cg.vSplits["w"].keys())
      this.vSplits["w"].set(key, cg.vSplits["w"].get(key).slice());
    this.joins = {b: new Map(), w: new Map()};
    for(let key of cg.joins["b"].keys())
      this.joins["b"].set(key, cg.joins["b"].get(key).slice());
    for(let key of cg.joins["w"].keys())
      this.joins["w"].set(key, cg.joins["w"].get(key).slice());
    this.neighbors = new Map();
    for(let key of cg.neighbors.keys())
      this.neighbors.set(key, new Set(cg.neighbors.get(key)));
    this.corners = new Map();
    for(let key of cg.corners.keys())
      this.corners.set(key, new Set(cg.corners.get(key)));
    this.chains = new Set();
    for(let chain of cg.chains.keys())
      this.chains.add(new Set(chain));
    this.notationMap = new Map(cg.notationMap);
    this.action = cg.action;
    this.notation = cg.notation;
    this.winner = cg.winner;
    this.history = cg.history.slice();
    this.historyIndex = cg.historyIndex;
    this.horizontalSort = cg.horizontalSort.slice();
    this.verticalSort = cg.verticalSort.slice();
  }

  getPossibleMoves(){
  /* returns list of valid moves given current game state*/
    return this.getMoves();
  }

  // Clears and updates hSplits, vSplits and joins based the current board pieces
  updateSplitsJoins() {
    this.hSplits["b"].clear();
    this.hSplits["w"].clear();
    this.vSplits["b"].clear();
    this.vSplits["w"].clear();
    for(let cp of this.board.blMap.values()) {
      var h = cp.tr.y - cp.bl.y;
      var w = cp.tr.x - cp.bl.x;
      if(w == h || h == 2*w) {
        this.hSplits[cp.color].set(CPoint.make(cp.bl.x, cp.bl.y + h/2), [cp]);
      }
      if(w == h || w == 2*h) {
        this.vSplits[cp.color].set(CPoint.make(cp.bl.x + w/2, cp.bl.y), [cp]);
      }
    }
    for(let color of ["b", "w"]) {
      for(let k of this.hSplits[color].keys()) {
        var x = this.hSplits[color].get(k)[0].tr.x;
        var y = k.y;
        while(true) {
          var next = this.board.blMap.get(CPoint.make(x, y));
          while(next) {
            x = next.tr.x;
            next = this.board.blMap.get(CPoint.make(x, y));
          }
          next = this.hSplits[color].get(CPoint.make(x, y));
          if(!next) {
            break;
          }
          this.hSplits[color].get(k).push(next[0]);
          x = next[0].tr.x;
        }
      }
      for(let k of this.vSplits[color].keys()) {
        var y = this.vSplits[color].get(k)[0].tr.y;
        var x = k.x;
        while(true) {
          var next = this.board.blMap.get(CPoint.make(x, y));
          while(next) {
            y = next.tr.y;
            next = this.board.blMap.get(CPoint.make(x, y));
          }
          next = this.vSplits[color].get(CPoint.make(x, y));
          if(!next) {
            break;
          }
          this.vSplits[color].get(k).push(next[0]);
          y = next[0].tr.y;
        }
      }
    }

    this.joins["b"].clear();
    this.joins["w"].clear();
    for(let cp of this.board.blMap.values()) {
      var bottomArr = [];
      var right = cp;
      var x1 = cp.bl.x;
      var y1 = cp.bl.y;
      var x2, y2;
      while(right && right.color == cp.color) {
        bottomArr.push(right);
        x2 = right.tr.x;
        var stack = bottomArr.slice();
        var arr = [];
        var nextBottom = [];
        for(let factor of [0.5, 1, 2]) {
          y2 = (x2 - x1) * factor + y1;
          var valid = true;
          while(stack.length >= 1) {
            var top = stack.pop();
            if(top.color != cp.color || top.tr.y > y2 || top.tr.x > x2) {
              valid = false;
              break;
            } else if(top.tr.y == y2) {
              if(top.tr.x != x2) {
                var r = this.board.blMap.get(CPoint.make(top.tr.x, top.bl.y));
                if(r)
                  stack.push(r);
              }
              nextBottom.push(top);
            } else if(top.tr.x == x2) {
              var u = this.board.blMap.get(CPoint.make(top.bl.x, top.tr.y));
              if(u)
                stack.push(u);
            } else {
              var r = this.board.blMap.get(CPoint.make(top.tr.x, top.bl.y));
              if(r)
                stack.push(r);
              var u = this.board.blMap.get(CPoint.make(top.bl.x, top.tr.y));
              if(u)
                stack.push(u);
            }
            if(!arr.find((cp) => {return cp == top;}))
              arr.push(top);
          }
          stack = bottomArr.slice();
          if(!valid)
            continue;
          nextBottom = [];
          if(arr.length > 1) {
            var maxTr = arr[0].tr;
            for(let cp of arr) {
              if(cp.tr.x > maxTr.x || cp.tr.y > maxTr.y)
                maxTr = cp.tr;
            }
            if(maxTr.x != x2 || maxTr.y != y2)
              continue;
            var area = 0;
            for(let cp of arr) {
              area += (cp.tr.x - cp.bl.x) * (cp.tr.y - cp.bl.y);
            }
            if(area != (x2 - x1) * (y2 - y1))
              continue;
            if(!this.joins[cp.color].has(cp.bl))
              this.joins[cp.color].set(cp.bl, []);
            this.joins[cp.color].get(cp.bl).push([maxTr, arr.slice()]);
          }
        }
        right = this.board.blMap.get(CPoint.make(right.tr.x, right.bl.y));
      }
    }
  }

  // Splits all of the given pieces in the given direction and return the new pieces
  // Arguments: array of pieces to split, direction to split ("h" for horizontal, "v" for vertical)
  split(pieces, dir) {
    this.notation = this.notationMap.get(pieces[0]).toString() + (dir == "h" ? "H" : "V");
    if(pieces.length > 1)
      this.notation += pieces.length;
    var oldPieces = [];
    var newPieces = [];
    for(var i = 0; i < pieces.length; i++) {
      var cp = pieces[i];
      if(dir == "v") {
        var x = (cp.bl.x + cp.tr.x) / 2;
        var p1 = CPiece.make(cp.bl, CPoint.make(x, cp.tr.y), cp.color);
        var p2 = CPiece.make(CPoint.make(x, cp.bl.y), cp.tr, cp.color);
      } else if(dir == "h") {
        var y = (cp.bl.y + cp.tr.y) / 2;
        var p1 = CPiece.make(cp.bl, CPoint.make(cp.tr.x, y), cp.color);
        var p2 = CPiece.make(CPoint.make(cp.bl.x, y), cp.tr, cp.color);
      }
      oldPieces.push(cp);
      newPieces.push(p1);
      newPieces.push(p2);
    }
    this.replacePieces(oldPieces, newPieces);
    this.updateNotations();
    return newPieces;
  }

  // Swaps the given pieces and returns the new piece that can be swapped from next
  // Arguments: piece to swap from, piece to swap into
  swap(p1, p2) {
    var newP1 = CPiece.make(p1.bl, p1.tr, p2.color);
    var newP2 = CPiece.make(p2.bl, p2.tr, p1.color);
    this.replacePieces([p1, p2], [newP1, newP2]);
    this.swapNotations(p1, newP1);
    this.swapNotations(p2, newP2);
    var captured = this.doCaptures();
    newP2 = this.board.blMap.get(newP2.bl);
    this.winner = this.checkWin();
    if(this.autoWin && this.winner) {
    //   console.log(this.notation);
    //   console.log(this.winner);
      this.action = "done";
      newP2 = undefined;
    }
    return newP2;
  }

  // Joins the given pieces and returns the resulting piece
  // Arguments: pieces to join together
  join(pieces) {
    var bl = pieces[0].bl;
    var tr = pieces[0].tr;
    for(let cp of pieces) {
      if(cp.bl.x < bl.x || cp.bl.y < bl.y)
        bl = cp.bl;
      if(cp.tr.x > tr.x || cp.tr.y > tr.y)
        tr = cp.tr;
    }
    var dx = 1;
    for(var i = this.horizontalSort.findIndex(x => {return x == bl}) + 1; this.horizontalSort[i].x < tr.x; i++)
      dx++;
    var dy = 1;
    for(var i = this.verticalSort.findIndex(x => {return x == CPoint.make(tr.x, bl.y)}) + 1; this.verticalSort[i].y < tr.y; i++)
      dy++;
    this.notation = this.notationMap.get(this.board.blMap.get(bl)).toString() + "J" + dx + "," + dy;
    var newCp = CPiece.make(bl, tr, pieces[0].color);
    this.replacePieces(pieces, [newCp]);
    this.updateNotations();
    return newCp;
  }

  // Deletes the old pieces and adds the new pieces to the board
  // Arguments: array of pieces to remove from board, array of pieces to add
  replacePieces(oldPieces, newPieces) {
    for(let cp of oldPieces) {
      this.board.blMap.delete(cp.bl);
      this.board.trMap.delete(cp.tr);
    }
    for(let cp of newPieces) {
      this.board.blMap.set(cp.bl, cp);
      this.board.trMap.set(cp.tr, cp);
    }
    this.updateNeighborsCorners(oldPieces, newPieces);
    this.updateChains(oldPieces, newPieces);
  }

  updateNeighborsCorners(oldPieces, newPieces) {
    for(let cp of oldPieces) {
      for(let neighbor of this.neighbors.get(cp)) {
        this.neighbors.get(neighbor).delete(cp);
      }
      for(let corner of this.corners.get(cp)) {
        this.corners.get(corner).delete(cp);
      }
      this.neighbors.delete(cp);
      this.corners.delete(cp);
    }
    for(let cp1 of newPieces) {
      if(!this.neighbors.has(cp1)) {
        this.neighbors.set(cp1, new Set());
        this.corners.set(cp1, new Set());
      }
    }
    for(let cp1 of newPieces) {
      for(let cp2 of this.board.blMap.values()) {
        if(cp1.isNeighbor(cp2)) {
          this.neighbors.get(cp1).add(cp2);
          this.neighbors.get(cp2).add(cp1);
        } else if(cp1.isCorner(cp2)) {
          this.corners.get(cp1).add(cp2);
          this.corners.get(cp2).add(cp1);
        }
      }
    }
  }

  updateChains(oldPieces, newPieces) {
    var toAddToChains = new Set(newPieces);
    for(let cp of oldPieces) {
      var chain = this.findChain(cp);
      if(chain) {
        for(let cp2 of chain)
          toAddToChains.add(cp2);
        this.chains.delete(chain);
      }
      toAddToChains.delete(cp);
    }
    while(toAddToChains.size > 0) {
      var newChain = new Set();
      var cp = toAddToChains.keys().next().value;
      toAddToChains.delete(cp);
      var toAddtoNew = new Set();
      toAddtoNew.add(cp);
      while(toAddtoNew.size > 0) {
        var next = toAddtoNew.keys().next().value;
        toAddtoNew.delete(next);
        toAddToChains.delete(next);
        newChain.add(next);
        for(let neighbor of this.neighbors.get(next)) {
          if(!newChain.has(neighbor)) {
            var chain = this.findChain(neighbor);
            if(chain)
              this.chains.delete(chain);
            toAddtoNew.add(neighbor);
          }
        }
      }
      this.chains.add(newChain);
    }
  }

  findChain(cp) {
    for(let chain of this.chains) {
      if(chain.has(cp))
        return chain;
    }
    return undefined;
  }

  doCaptures() {
    var chainsToCheck = new Set(this.chains);
    var it = chainsToCheck.keys();
    for(var c = it.next(); !c.done; c = it.next()) {
      var chain = c.value;
      for(var cp of chain) {
        if(cp.bl.x == 0 || cp.bl.y == 0 || cp.tr.x == this.board.width || cp.tr.y == this.board.height) {
          chainsToCheck.delete(chain);
          break;
        }
      }
    }
    var prevSize = -1;
    while(chainsToCheck.size != prevSize) {
      prevSize = chainsToCheck.size;
      var it = chainsToCheck.keys();
      for(var c = it.next(); !c.done; c = it.next()) {
        var chain = c.value;
        for(let cp of chain) {
          var shortcut = false;
          for(let corner of this.corners.get(cp)) {
            if(!chainsToCheck.has(this.findChain(corner))) {
              chainsToCheck.delete(chain);
              shortcut = true;
              break;
            }
          }
          if(shortcut)
            break;
        }
      }
    }
    var oldPieces = [];
    var newPieces = [];
    for(let chain of chainsToCheck) {
      for(let cp of chain) {
        var newCP = CPiece.make(cp.bl, cp.tr, cp.color == "b" ? "w" : "b");
        oldPieces.push(cp);
        newPieces.push(newCP);
        this.swapNotations(cp, newCP);
      }
    }
    if(oldPieces.length > 0) {
      this.replacePieces(oldPieces, newPieces);
    }
    return oldPieces;
  }

  checkWin() {
    for(let chain of this.chains) {
      var walls = new Set();
      for(let cp of chain) {
        if(cp.bl.x == 0)
          walls.add("w");
        else if(cp.tr.x == this.board.width)
          walls.add("e");
        if(cp.bl.y == 0)
          walls.add("s");
        else if(cp.tr.y == this.board.height)
          walls.add("n");
      }
      if(walls.size == 4)
        return chain.keys().next().value.color;
    }
    return undefined;
  }

  updateNotations() {
    var s = new Set();
    for(let cp of this.board.blMap.values()) {
      s.add(cp.bl);
      s.add(cp.tr);
      s.add(CPoint.make(cp.bl.x, cp.tr.y));
      s.add(CPoint.make(cp.tr.x, cp.bl.y));
    }
    this.verticalSort = [];
    for(let cp of s) {
      this.verticalSort.push(cp);
    }
    this.horizontalSort = this.verticalSort.slice();
    this.verticalSort.sort((cp1, cp2) => {
      if(cp1.x < cp2.x) {
        return -1;
      } else if(cp1.x == cp2.x) {
        if(cp1.y < cp2.y) {
          return -1;
        } else if(cp1.y == cp2.y) {
          return 0;
        } else {
          return 1;
        }
      } else {
        return 1;
      }
    });
    this.horizontalSort.sort((cp1, cp2) => {
      if(cp1.y < cp2.y) {
        return -1;
      } else if(cp1.y == cp2.y) {
        if(cp1.x < cp2.x) {
          return -1;
        } else if(cp1.x == cp2.x) {
          return 0;
        } else {
          return 1;
        }
      } else {
        return 1;
      }
    });

    var origin = this.board.blMap.get(CPoint.make(0, 0));
    this.notationMap.clear();
    this.notationMap.set(origin, new CNotation([0]));
    var stack = [{cp: origin, dir: "r"}];
    while(stack.length > 0) {
      var top = stack.pop();
      var cp = top.cp;
      var dir = top.dir;
      var up = this.board.blMap.get(CPoint.make(cp.bl.x, cp.tr.y));
      var right = this.board.blMap.get(CPoint.make(cp.tr.x, cp.bl.y));
      if(dir == "r") {
        if(up && !this.notationMap.has(up)) {
          var between = 1;
          for(var i = this.verticalSort.findIndex(x => {return x == cp.bl}) + 1; this.verticalSort[i].y < cp.tr.y; i++)
            between++;
          this.notationMap.set(up, this.notationMap.get(cp).up(between));
          stack.push({cp: up, dir: "u"});
        }
      }
      if(right && !this.notationMap.has(right)) {
        var between = 1;
        for(var i = this.horizontalSort.findIndex(x => {return x == cp.bl}) + 1; this.horizontalSort[i].x < cp.tr.x; i++)
          between++;
        this.notationMap.set(right, this.notationMap.get(cp).right(between));
        stack.push({cp: right, dir: "r"});
      }
      if(dir != "r") {
        if(up && !this.notationMap.has(up)) {
          var between = 1;
          for(var i = this.verticalSort.findIndex(x => {return x == cp.bl}) + 1; this.verticalSort[i].y < cp.tr.y; i++)
            between++;
          this.notationMap.set(up, this.notationMap.get(cp).up(between));
          stack.push({cp: up, dir: "u"});
        }
      }
    }
  }

  swapNotations(oldPiece, newPiece) {
    this.notationMap.set(newPiece, this.notationMap.get(oldPiece));
    this.notationMap.delete(oldPiece);
  }

  doMove(notation, canvas, icanvas) {
    if(notation.length == 0)
      return;
    if(this.winner)
      return;
    var re = /(\d+(?:,\d+)*)J(\d+),(\d+)([NESW]*)$/;
    var match = re.exec(notation);
    if(match) {
      var start = this.findPiece(match[1]);
      var end = this.pointRightUp(start.bl, parseInt(match[2]), parseInt(match[3]));
      var arr = this.joins[this.turn].get(start.bl);
      for(var i = 0; i < arr.length; i++) {
        if(arr[i][0] == end) {
          swapStart = this.join(arr[i][1]);
          break;
        }
      }
      var swaps = match[4];
    }
    re = /(\d+(?:,\d+)*)([VH])([NESW]-)*([NESW]*)$/;
    match = re.exec(notation);
    if(match) {
      var cp = this.findPiece(match[1]);
      if(match[2] == "V") {
        this.swapStarts = this.split(this.vSplits[this.turn].get(CPoint.make((cp.bl.x + cp.tr.x) / 2, cp.bl.y)).slice(0, 1), match[2].toLowerCase());
      } else if(match[2] == "H") {
        this.swapStarts = this.split(this.hSplits[this.turn].get(CPoint.make(cp.bl.x, (cp.bl.y + cp.tr.y) / 2)).slice(0, 1), match[2].toLowerCase());
      }
      var swaps = match[4];
      if(match[3]) {
        if(match[3].charAt(0) == "N" || match[3].charAt(0) == "E") {
          var swapStart = this.swapStarts[1];
        } else {
          var swapStart = this.swapStarts[0];
        }
      } else if(swaps.length > 0) {
        if(swaps[0] == "N" || swaps[0] == "E") {
          var swapStart = this.swapStarts[1];
        } else {
          var swapStart = this.swapStarts[0];
        }
      }
    }
    re = /(\d+(?:,\d+)*)([VH])(\d+)-*(\d+)*([NESW])*-*([NESW]*)$/;
    match = re.exec(notation);
    if(match) {
      var cp = this.findPiece(match[1]);
      if(match[2] == "V") {
        this.swapStarts = this.split(this.vSplits[this.turn].get(CPoint.make((cp.bl.x + cp.tr.x) / 2, cp.bl.y)).slice(0, parseInt(match[3])), match[2].toLowerCase());
      } else if(match[2] == "H") {
        this.swapStarts = this.split(this.hSplits[this.turn].get(CPoint.make(cp.bl.x, (cp.bl.y + cp.tr.y) / 2)).slice(0, parseInt(match[3])), match[2].toLowerCase());
      }
      var swaps = match[6];
      if(swaps.length > 0) {
        if(match[5] == "N" || match[5] == "E") {
          var swapStart = this.swapStarts[2*parseInt(match[4])-1];
        } else {
          var swapStart = this.swapStarts[2*parseInt(match[4])-2];
        }
      }
    }
    var ret = undefined;
    if(swapStart)
      var color = swapStart.color;
    for(let dir of swaps) {
      if(dir == "N") {
        cp = this.board.blMap.get(CPoint.make(swapStart.bl.x, swapStart.tr.y));
      } else if(dir == "E") {
        cp = this.board.blMap.get(CPoint.make(swapStart.tr.x, swapStart.bl.y));
      } else if(dir == "S") {
        cp = this.board.trMap.get(CPoint.make(swapStart.tr.x, swapStart.bl.y));
      } else if(dir == "W") {
        cp = this.board.trMap.get(CPoint.make(swapStart.bl.x, swapStart.tr.y));
      }
      if(cp.color == swapStart.color)
        break;
      swapStart = this.swap(swapStart, cp);
      if(swapStart.color != color)
        break;
      if(this.autoWin && this.winner)
        break;
    }
    this.turn = this.turn == "w" ? "b" : "w";
    // console.log(notation);
    if(this.winner) {
    //   console.log(this.winner);
      this.action = "done";
    } else {
      this.action = "";
    }
    this.updateSplitsJoins();
    if(canvas)
        this.board.draw(canvas, this.notationMap, this.showNotations);
    if(icanvas)
      icanvas.getContext("2d").clearRect(0, 0, icanvas.width, icanvas.height);
    this.notation = notation;
    this.history.push(notation);
    this.historyIndex++;
  }

  undo(table) {
    if(this.historyIndex == 0)
      return false;
    var moves = this.history.slice();
    var index = this.historyIndex - 1;
    if(table)
      table.rows[Math.floor((this.historyIndex - 1) / 2)].cells[((this.historyIndex - 1) % 2) + 1].style.backgroundColor = "initial";
    this.board.reset();
    this.reset();
    for(var i = 0; i < index; i++) {
      this.doMove(moves[i]);
    }
    this.history = moves;
    if(table && this.historyIndex > 0)
      table.rows[Math.floor((this.historyIndex - 1) / 2)].cells[((this.historyIndex - 1) % 2) + 1].style.backgroundColor = "yellow";
    return true;
  }

  redo(table) {
    if(this.historyIndex == this.history.length)
      return false;
    var moves = this.history.slice();
    var index = this.historyIndex + 1;
    if(table && this.historyIndex > 0)
      table.rows[Math.floor((this.historyIndex - 1) / 2)].cells[((this.historyIndex - 1) % 2) + 1].style.backgroundColor = "initial";
    this.board.reset();
    this.reset();
    for(var i = 0; i < index; i++) {
      this.doMove(moves[i]);
    }
    this.history = moves;
    if(table)
      table.rows[Math.floor((this.historyIndex - 1) / 2)].cells[((this.historyIndex - 1) % 2) + 1].style.backgroundColor = "yellow";
    return true;
  }

  findPiece(notation) {
    for(var cp of this.notationMap.keys()) {
      if(this.notationMap.get(cp) == notation)
        return cp;
    }
    return undefined;
  }

  pointRightUp(start, right, up) {
    var index = this.horizontalSort.findIndex(x => {return x == start}) + right;
    start = this.horizontalSort[index];
    index = this.verticalSort.findIndex(x => {return x == start}) + up;
    return this.verticalSort[index];
  }

  getMoves() {
    let legalPlays = []
    for(let start of this.hSplits[this.turn].keys()) {
      var arr = this.hSplits[this.turn].get(start);
      var swapStart = CPiece.make(arr[0].bl, CPoint.make(arr[0].tr.x, (arr[0].tr.y + arr[0].bl.y) / 2), arr[0].color);
      var swaps = this.getSwaps(swapStart);
      for(let notation of swaps) {
        if(notation.length == 0 || notation.charAt(0) == "S") {
          legalPlays.push(this.notationMap.get(arr[0]) + "H" + notation);
        } else {
          legalPlays.push(this.notationMap.get(arr[0]) + "HS-" + notation);
        }
      }
      var swapStart = CPiece.make(CPoint.make(arr[0].bl.x, (arr[0].tr.y + arr[0].bl.y) / 2), arr[0].tr, arr[0].color);
      var swaps = this.getSwaps(swapStart);
      for(let notation of swaps) {
        if(notation.length == 0 || notation.charAt(0) == "N") {
          legalPlays.push(this.notationMap.get(arr[0]) + "H" + notation);
        } else {
          legalPlays.push(this.notationMap.get(arr[0]) + "HN-" + notation);
        }
      }
      for(var i = 1; i < arr.length; i++) {
        legalPlays.push(this.notationMap.get(arr[0]) + "H" + (i+1));
        for(var j = 0; j <= i; j++) {
          var swapStart = CPiece.make(arr[j].bl, CPoint.make(arr[j].tr.x, (arr[j].tr.y + arr[j].bl.y) / 2), arr[j].color);
          var swaps = this.getSwaps(swapStart);
          for(let notation of swaps) {
            if(notation.length == 0) {
              legalPlays.push(this.notationMap.get(arr[0]) + "H" + (i+1));
            } else {
              legalPlays.push(this.notationMap.get(arr[0]) + "H" + (i+1) + "-" + (j+1) + "S-" + notation);
            }
          }
          var swapStart = CPiece.make(CPoint.make(arr[j].bl.x, (arr[j].tr.y + arr[j].bl.y) / 2), arr[j].tr, arr[j].color);
          var swaps = this.getSwaps(swapStart);
          for(let notation of swaps) {
            if(notation.length == 0) {
              legalPlays.push(this.notationMap.get(arr[0]) + "H" + (i+1));
            } else {
              legalPlays.push(this.notationMap.get(arr[0]) + "H" + (i+1) + "-" + (j+1) + "N-" + notation);
            }
          }
        }
      }
    }
    for(let start of this.vSplits[this.turn].keys()) {
      var arr = this.vSplits[this.turn].get(start);
      var swapStart = CPiece.make(arr[0].bl, CPoint.make((arr[0].tr.x + arr[0].bl.x) / 2, arr[0].tr.y), arr[0].color);
      var swaps = this.getSwaps(swapStart);
      for(let notation of swaps) {
        if(notation.length == 0 || notation.charAt(0) == "W") {
          legalPlays.push(this.notationMap.get(arr[0]) + "V" + notation);
        } else {
          legalPlays.push(this.notationMap.get(arr[0]) + "VW-" + notation);
        }
      }
      var swapStart = CPiece.make(CPoint.make((arr[0].bl.x + arr[0].tr.x) / 2, arr[0].bl.y), arr[0].tr, arr[0].color);
      var swaps = this.getSwaps(swapStart);
      for(let notation of swaps) {
        if(notation.length == 0 || notation.charAt(0) == "E") {
          legalPlays.push(this.notationMap.get(arr[0]) + "V" + notation);
        } else {
          legalPlays.push(this.notationMap.get(arr[0]) + "VE-" + notation);
        }
      }
      for(var i = 1; i < arr.length; i++) {
        legalPlays.push(this.notationMap.get(arr[0]) + "V" + (i+1));
        for(var j = 0; j <= i; j++) {
          var swapStart = CPiece.make(arr[j].bl, CPoint.make((arr[j].tr.x + arr[j].bl.x) / 2, arr[j].tr.y), arr[j].color);
          var swaps = this.getSwaps(swapStart);
          for(let notation of swaps) {
            if(notation.length == 0) {
              legalPlays.push(this.notationMap.get(arr[0]) + "V" + (i+1));
            } else {
              legalPlays.push(this.notationMap.get(arr[0]) + "V" + (i+1) + "-" + (j+1) + "W-" + notation);
            }
          }
          var swapStart = CPiece.make(CPoint.make((arr[j].bl.x + arr[j].tr.x) / 2, arr[j].bl.y), arr[j].tr, arr[j].color);
          var swaps = this.getSwaps(swapStart);
          for(let notation of swaps) {
            if(notation.length == 0) {
              legalPlays.push(this.notationMap.get(arr[0]) + "V" + (i+1));
            } else {
              legalPlays.push(this.notationMap.get(arr[0]) + "V" + (i+1) + "-" + (j+1) + "E-" + notation);
            }
          }
        }
      }
    }
    for(let bl of this.joins[this.turn].keys()) {
      var tr = this.joins[this.turn].get(bl)[0][0];
      var pieces = this.joins[this.turn].get(bl)[0][1];
//       var bl = pieces[0].bl;
//       var tr = pieces[0].tr;
//       for(let cp of pieces) {
//         if(cp.bl.x < bl.x || cp.bl.y < bl.y)
//           bl = cp.bl;
//         if(cp.tr.x > tr.x || cp.tr.y > tr.y)
//           tr = cp.tr;
//       }
      var dx = 1;
      for(var i = this.horizontalSort.findIndex(x => {return x == bl}) + 1; this.horizontalSort[i].x < tr.x; i++)
        dx++;
      var dy = 1;
      for(var i = this.verticalSort.findIndex(x => {return x == CPoint.make(tr.x, bl.y)}) + 1; this.verticalSort[i].y < tr.y; i++)
        dy++;
      var swapStart = CPiece.make(bl, tr, pieces[0].color);
      var swaps = this.getSwaps(swapStart);
      for(let notation of swaps) {
        legalPlays.push(this.notationMap.get(this.board.blMap.get(bl)) + "J" + dx + "," + dy + notation);
      }
    }
    return legalPlays
  }

  getSwaps(cp, visited, notation, color) {
    if(visited === undefined) {
      visited = new Set();
      notation = "";
      color = cp.color;
    }
    visited.add(cp);
    var ret = [notation];
    var tl = CPoint.make(cp.bl.x, cp.tr.y);
    var br = CPoint.make(cp.tr.x, cp.bl.y);
    var trMap = this.board.trMap;
    var blMap = this.board.blMap;
    // get the piece down
    var newCp = trMap.get(br);
    if(newCp && newCp.bl.x == cp.bl.x && color != newCp.color && !visited.has(newCp))
      ret = ret.concat(this.getSwaps(newCp, new Set(visited), notation + "S", color));
    // get the piece left
    newCp = trMap.get(tl);
    if(newCp && newCp.bl.y == cp.bl.y && color != newCp.color && !visited.has(newCp))
      ret = ret.concat(this.getSwaps(newCp, new Set(visited), notation + "W", color));
    // get the piece up
    newCp = blMap.get(tl);
    if(newCp && newCp.tr.x == cp.tr.x && color != newCp.color && !visited.has(newCp))
      ret = ret.concat(this.getSwaps(newCp, new Set(visited), notation + "N", color));
    // get the piece right
    newCp = blMap.get(br);
    if(newCp && newCp.tr.y == cp.tr.y && color != newCp.color && !visited.has(newCp))
      ret = ret.concat(this.getSwaps(newCp, new Set(visited), notation + "E", color));
    return ret;
  }
}

// Creates and attaches event listeners to the proper canvases
// and draws the provided crumble board
// Requirements: The crumble-canvas and interactive-canvas HTML canvases must exist
function setupGraphics(cgame, canvas, icanvas, autoResize, gameId, table, playAs) {
  var gameRules = {
    listMoves : listMoves,
    nextState : nextState,
    terminalStateEval : terminalStateEval,
  }
  function startAI() {
    if(cgame.winner || cgame.turn != playAs)
      return;
    var depth = $("#slider-depth").slider("value");
    var alphabeta = minmax(cgame, gameRules, evaluate, depth);
    console.log(alphabeta.bestMove, depth, alphabeta.evaluation);
    var moves = getCookie("moves");
    if(cgame.historyIndex < cgame.history.length) {
      moves = moves.split("/").slice(0,cgame.historyIndex).join("/");
    }
    if(!moves) {
      moves = alphabeta.bestMove;
    } else {
      moves += "/" + alphabeta.bestMove;
    }
    setCookie("moves", moves, 24*7);
    setCookie("boardWidth", cgame.board.width, 24*7);
    setCookie("boardHeight", cgame.board.height, 24*7);
    setCookie("extra", cgame.board.extra, 24*7);
    setCookie("ai-player", playAs, 24*7);
    if(cgame.historyIndex > 0)
      table.rows[Math.floor((cgame.historyIndex - 1) / 2)].cells[((cgame.historyIndex - 1) % 2) + 1].style.backgroundColor = "initial";
    var rows = table.rows;
    var lastRow = rows[rows.length-1];
    if(!lastRow || lastRow.cells.length == 3) {
      lastRow = table.insertRow();
      lastRow.insertCell();
      lastRow.cells[0].innerText = rows.length + ".";
    }
    lastRow.insertCell();
    lastRow.cells[lastRow.cells.length-1].innerText = alphabeta.bestMove;
    table.rows[Math.floor(cgame.historyIndex / 2)].cells[(cgame.historyIndex % 2) + 1].style.backgroundColor = "yellow";
    cgame.doMove(alphabeta.bestMove);
    if(canvas)
      cgame.board.draw(canvas, cgame.notationMap, cgame.showNotations);
  }
  if(playAs == "b") {
    startAI();
  }

  function resizeCanvases() {
    var w = $(window).width();
    var h = $(window).height();
    var wRatio = w / resizeCanvases.prev.width;
    var hRatio = h / resizeCanvases.prev.height;
    var prevRatio = wRatio / hRatio;
    resizeCanvases.prev.height = h;
    resizeCanvases.prev.width = w;
    // This if statment allows the user to zoom in and out using the browser zoom feature
    // How it works: if the window width and height change by the same proportion, then
    // the resize was most likely from the browser zoom changing. This can also occur
    // if the window size was changed by a very small amount manually so we need to check
    // that at least one of the dimensions changed by a significant amount
    if((hRatio < .95 || hRatio > 1.05 || wRatio < .95 || wRatio > 1.05) && prevRatio < 1.01 && prevRatio > 0.99)
      return;
    // Adjust the canvas size for the whitespace on the left and the header
    w -= 30;
    h -= 150;
    // This is equal to the ratio of the board height to width
    // because the canvas is given 100 pixels for each square at the start
    var ratio = canvas.height / canvas.width;
    if(ratio == 1) {
      if(w < h) {
        h = w;
      } else {
        w = h;
      }
    } else {
      if(w * ratio < h) {
        h = w * ratio;
      } else {
        w = h / ratio;
      }
    }
    canvas.style.width = w + "px";
    canvas.style.height = h + "px";
    if(icanvas) {
      icanvas.style.width = w + "px";
      icanvas.style.height = h + "px";
    }
  }
  resizeCanvases.prev = {width: $(window).width(), height: $(window).height};
  $(window).on('resize', e => {
    if(autoResize)
      resizeCanvases();
  });

  function findNearestStart(x, y) {
    var minDist = Number.MAX_SAFE_INTEGER;
    var ret = {};
    ret.action = "";
    for(let cp of cgame.hSplits[cgame.turn].keys()) {
      var p = cp.toPoint(icanvas, cgame.board.height, cgame.board.width);
      var dist = Math.pow(p.x - x, 2) + Math.pow(p.y - y, 2);
      if(dist < minDist) {
        ret.px = p.x;
        ret.py = p.y;
        minDist = dist;
        ret.action = "h";
        ret.cp = cp;
      }
    }
    for(let cp of cgame.vSplits[cgame.turn].keys()) {
      var p = cp.toPoint(icanvas, cgame.board.height, cgame.board.width);
      var dist = Math.pow(p.x - x, 2) + Math.pow(p.y - y, 2);
      if(dist < minDist) {
        ret.px = p.x;
        ret.py = p.y;
        minDist = dist;
        ret.action = "v";
        ret.cp = cp;
      }
    }
    for(let cp of cgame.joins[cgame.turn].keys()) {
      var p = cp.toPoint(icanvas, cgame.board.height, cgame.board.width);
      var dist = Math.pow(p.x - x, 2) + Math.pow(p.y - y, 2);
      if(dist < minDist) {
        ret.px = p.x;
        ret.py = p.y;
        minDist = dist;
        ret.action = "j";
        ret.cp = cp;
      }
    }
    return ret;
  }

  function findNearestEnd(x, y, action, start) {
    var minDist = Number.MAX_SAFE_INTEGER;
    var ret = {};
    if(action == "h") {
      var arr = cgame.hSplits[cgame.turn].get(start);
    } else if(action == "v") {
      var arr = cgame.vSplits[cgame.turn].get(start);
    } else if(action == "j") {
      var arr = cgame.joins[cgame.turn].get(start);
    }
    ret.arr = [];
    for(var i = 0; i < arr.length; i++) {
      if(action == "h") {
        var p = CPoint.make(arr[i].tr.x, (arr[i].tr.y + arr[i].bl.y) / 2).toPoint(icanvas, cgame.board.height, cgame.board.width);
      } else if(action == "v") {
        var p = CPoint.make((arr[i].tr.x + arr[i].bl.x) / 2, arr[i].tr.y).toPoint(icanvas, cgame.board.height, cgame.board.width);
      } else if(action == "j") {
        var p = arr[i][0].toPoint(icanvas, cgame.board.height, cgame.board.width);
      }
      var dist = Math.pow(p.x - x, 2) + Math.pow(p.y - y, 2);
      if(dist < minDist) {
        ret.px = p.x;
        ret.py = p.y;
        minDist = dist;
        if(action == "j") {
          ret.arr = arr[i][1];
        } else {
          while(ret.arr.length <= i) {
            ret.arr.push(arr[ret.arr.length]);
          }
        }
      }
    }
    return ret;
  }

  function findPieceBelow(x, y, swapStarts) {
    if(swapStarts.length == 1) {
      var cp = swapStarts[0];
      var trMap = cgame.board.trMap;
      var blMap = cgame.board.blMap;
      var br = CPoint.make(cp.tr.x, cp.bl.y);
      var tl = CPoint.make(cp.bl.x, cp.tr.y);
      var arr = [];
      // get the piece down
      var newCp = trMap.get(br);
      if(newCp && newCp.bl.x == cp.bl.x && cp.color != newCp.color)
        arr.push(newCp);
      else
        arr.push(undefined);
      // get the piece left
      newCp = trMap.get(tl);
      if(newCp && newCp.bl.y == cp.bl.y && cp.color != newCp.color)
        arr.push(newCp);
      else
        arr.push(undefined);
      // get the piece up
      newCp = blMap.get(tl);
      if(newCp && newCp.tr.x == cp.tr.x && cp.color != newCp.color)
        arr.push(newCp);
      else
        arr.push(undefined);
      // get the piece right
      newCp = blMap.get(br);
      if(newCp && newCp.tr.y == cp.tr.y && cp.color != newCp.color)
        arr.push(newCp);
      else
        arr.push(undefined);
      for(var i = 0; i < arr.length; i++) {
        if(!arr[i])
          continue;
        var p1 = arr[i].bl.toPoint(canvas, cgame.board.height, cgame.board.width);
        var p2 = arr[i].tr.toPoint(canvas, cgame.board.height, cgame.board.width);
        if(x < p2.x && x > p1.x && y < p1.y && y > p2.y) {
          if(i == 0) {
            if(cgame.notation.endsWith("HS-"))
              cgame.notation = cgame.notation.replace("-","");
            else
              cgame.notation += "S";
          } else if(i == 1) {
            if(cgame.notation.endsWith("VW-"))
              cgame.notation = cgame.notation.replace("-","");
            else
              cgame.notation += "W";
          } else if(i == 2) {
            if(cgame.notation.endsWith("HN-"))
              cgame.notation = cgame.notation.replace("-","");
            else
              cgame.notation += "N";
          } else if(i == 3) {
            if(cgame.notation.endsWith("VE-"))
              cgame.notation = cgame.notation.replace("-","");
            else
              cgame.notation += "E";
          }
          return arr[i];
        }
      }
    } else {
      for(var i = 0; i < swapStarts.length; i++) {
        var p1 = swapStarts[i].bl.toPoint(canvas, cgame.board.height, cgame.board.width);
        var p2 = swapStarts[i].tr.toPoint(canvas, cgame.board.height, cgame.board.width);
        if(x < p2.x && x > p1.x && y < p1.y && y > p2.y) {
          if(swapStarts.length > 2)
            cgame.notation += "-" + (Math.floor(i / 2) + 1);
          if(cgame.notation.includes("H")) {
            if(i % 2 == 0)
              cgame.notation += "S-";
            else
              cgame.notation += "N-";
          } else {
            if(i % 2 == 0)
              cgame.notation += "W-";
            else
              cgame.notation += "E-";
          }
          return swapStarts[i];
        }
      }
    }
    return undefined;
  }

  function drawSwapStarts(swapStarts) {
    for(var i = 0; i < swapStarts.length; i++) {
      swapStarts[i].draw(icanvas, cgame.board.height, cgame.board.width, "red");
    }
  }

  function mouseHandler(e, clicked) {
    const icontext = icanvas.getContext('2d');
    if(!e.offsetX) {
      var rect = e.target.getBoundingClientRect();
      var x  = (e.targetTouches[0].pageX - rect.left) * icanvas.width / icanvas.style.width.substr(0,icanvas.style.width.length-2);
      var y   = (e.targetTouches[0].pageY - rect.top) * icanvas.height / icanvas.style.height.substr(0,icanvas.style.height.length-2);
    } else {
      mouseHandler.prev.rawX = e.offsetX;
      mouseHandler.prev.rawY = e.offsetY;
      var x = Math.round(e.offsetX * icanvas.width / parseInt(icanvas.style.width));
      var y = Math.round(e.offsetY * icanvas.height / parseInt(icanvas.style.height));
    }

    const l = 4;
    icontext.fillStyle = "red";
//     icontext.clearRect(mouseHandler.prev.x-l, mouseHandler.prev.y-l, l*2, l*2);
//     icontext.fillRect(x-l, y-l, l*2, l*2);
//     mouseHandler.prev.x = x;
//     mouseHandler.prev.y = y;

    if(cgame.action == "") {
      var ret = findNearestStart(x, y);
      var px = ret.px+2;
      var py = ret.py+1;
    } else if(cgame.action == "s") {
    } else if(cgame.action == "v" || cgame.action == "h" || cgame.action == "j") {
      var ret = findNearestEnd(x, y, cgame.action, mouseHandler.selectionPoint);
      var px = ret.px+2;
      var py = ret.py+1;
    }

    if(clicked) {
      if(cgame.action == "") {
        mouseHandler.selectionPoint = ret.cp;
        cgame.action = ret.action;
        mouseHandler(e, false);
      } else if(cgame.action == "s") {
        var cp = findPieceBelow(x, y, cgame.swapStarts);
        if(cp) {
          if(cgame.swapStarts.length == 1) {
            cp = cgame.swap(cgame.swapStarts[0], cp);
            cgame.board.draw(canvas, cgame.notationMap, cgame.showNotations);
            if(cgame.autoWin && cgame.winner) {
              var moves = getCookie("moves");
              moves += "/" + cgame.notation;
              setCookie("moves", moves, 24*7);
              setCookie("boardWidth", cgame.board.width, 24*7);
              setCookie("boardHeight", cgame.board.height, 24*7);
              setCookie("extra", cgame.board.extra, 24*7);
            }
          }
          icontext.clearRect(0, 0, icanvas.width, icanvas.height);
          if(cp)
            cgame.swapStarts = [cp];
          else
            cgame.swapStarts = [];
          drawSwapStarts(cgame.swapStarts);
        }
      } else if(cgame.action == "j") {
        cgame.swapStarts = [cgame.join(ret.arr)];
        cgame.board.draw(canvas, cgame.notationMap, cgame.showNotations);
        cgame.action = "s";
        mouseHandler.selectionPoint = undefined;
        icontext.clearRect(0, 0, icanvas.width, icanvas.height);
        drawSwapStarts(cgame.swapStarts);
        mouseHandler(e, false);
      } else if(cgame.action == "v" || cgame.action == "h") {
        cgame.swapStarts = cgame.split(ret.arr, cgame.action);
        cgame.board.draw(canvas, cgame.notationMap, cgame.showNotations);
        cgame.action = "s";
        mouseHandler.selectionPoint = undefined;
        icontext.clearRect(0, 0, icanvas.width, icanvas.height);
        drawSwapStarts(cgame.swapStarts);
        mouseHandler(e, false);
      }
    } else {
      if(cgame.action == "") {
        if(px != mouseHandler.prev.px || py != mouseHandler.prev.py) {
          if(ret.action == "h") {
            px += l;
          } else if(ret.action == "v") {
            py -= l;
          } else if(ret.action == "j") {
            px += l;
            py -= l;
          }
          icontext.clearRect(mouseHandler.prev.px-l, mouseHandler.prev.py-l, l*2, l*2);
          icontext.fillRect(px-l, py-l, l*2, l*2);
          mouseHandler.prev.px = px;
          mouseHandler.prev.py = py;
        }
      } else if(cgame.action == "s") {
      } else if(cgame.action == "v" || cgame.action == "h" || cgame.action == "j") {
        if(px != mouseHandler.prev.px || py != mouseHandler.prev.py) {
          var sp = mouseHandler.selectionPoint.toPoint(icanvas, cgame.board.height, cgame.board.width);
          sp.x += 2;
          sp.y += 1;
          if(cgame.action == "h") {
            icontext.clearRect(sp.x+l, sp.y-l, mouseHandler.prev.px - sp.x - l, l*2);
            icontext.fillRect(sp.x+l, sp.y-l, px - sp.x - l, l*2);
          } else if(cgame.action == "v") {
            icontext.clearRect(sp.x-l, sp.y, l*2, mouseHandler.prev.py - sp.y);
            icontext.fillRect(sp.x-l, sp.y, l*2, py - sp.y);
          } else if(cgame.action == "j") {
//             icontext.clearRect(sp.x - icontext.lineWidth, sp.y + icontext.lineWidth, mouseHandler.prev.x - sp.x + 2*icontext.lineWidth, mouseHandler.prev.py - sp.y - 2*icontext.lineWidth);
            icontext.clearRect(0, 0, icanvas.width, icanvas.height);
            icontext.strokeStyle = "red";
            icontext.lineWidth = 4;
            icontext.strokeRect(sp.x, sp.y, px - sp.x, py - sp.y);
          }
          mouseHandler.prev.px = px;
          mouseHandler.prev.py = py;
        }
      } else if(cgame.action == "done") {
        icanvas.getContext("2d").clearRect(0, 0, icanvas.width, icanvas.height);
      }
    }
  }
  mouseHandler.prev = {x: 0, y: 0, px: -1, py: -1};
  mouseHandler.selectionPoint = undefined;
  if(icanvas) {
    icanvas.addEventListener('mousemove', e => {
      mouseHandler(e, false);
    });
    icanvas.addEventListener('click', e => {
      mouseHandler(e, true);
    });
    icanvas.addEventListener('touchmove', e => {
      if(e.touches.length == 1) {
        e.preventDefault();
        mouseHandler(e, false);
      }
    });
  }

  function endTurn() {
    if(cgame.action == "s") {
      if(cgame.notation.endsWith("-")) {
        var i = cgame.notation.indexOf("-");
        cgame.notation = cgame.notation.substr(0,i);
      }
      var moves = getCookie("moves");
      if(cgame.historyIndex < cgame.history.length) {
        moves = moves.split("/").slice(0,cgame.historyIndex).join("/");
      }
      if(!moves) {
        moves = cgame.notation;
      } else {
        moves += "/" + cgame.notation;
      }
      setCookie("moves", moves, 24*7);
      setCookie("boardWidth", cgame.board.width, 24*7);
      setCookie("boardHeight", cgame.board.height, 24*7);
      setCookie("extra", cgame.board.extra, 24*7);
      setCookie("ai-player", playAs, 24*7);
      if(cgame.historyIndex < cgame.history.length) {
        var extraRows = Math.floor((cgame.history.length - 1) / 2) - Math.floor((cgame.historyIndex - 1) / 2);
        cgame.history.splice(cgame.historyIndex);
        for(var i = 0; i < extraRows; i++) {
          table.deleteRow(table.rows.length - 1);
        }
        if(table.rows.length > 0 && cgame.historyIndex % 2 == 1)
          table.rows[table.rows.length-1].deleteCell(2);
      }
      if(cgame.historyIndex > 0)
        table.rows[Math.floor((cgame.historyIndex - 1) / 2)].cells[((cgame.historyIndex - 1) % 2) + 1].style.backgroundColor = "initial";
      var rows = table.rows;
      var lastRow = rows[rows.length-1];
      if(!lastRow || lastRow.cells.length == 3) {
          lastRow = table.insertRow();
          lastRow.insertCell();
          lastRow.cells[0].innerText = rows.length + ".";
      }
      lastRow.insertCell();
      lastRow.cells[lastRow.cells.length-1].innerText = cgame.notation;
      table.rows[Math.floor(cgame.historyIndex / 2)].cells[(cgame.historyIndex % 2) + 1].style.backgroundColor = "yellow";
      cgame.history.push(cgame.notation);
      cgame.historyIndex++;
    //   cgame.notation = "";
      if(cgame.winner) {
        // console.log(cgame.winner);
        cgame.action = "done";
      } else {
        cgame.action = "";
      }
      cgame.turn = cgame.turn == "w" ? "b" : "w";
      mouseHandler.prev.px = -1;
      mouseHandler.prev.py = -1;
      cgame.updateSplitsJoins();
      icanvas.getContext("2d").clearRect(0, 0, icanvas.width, icanvas.height);
      mouseHandler({offsetX: mouseHandler.prev.rawX, offsetY: mouseHandler.prev.rawY}, false);
    }
    startAI();
  }

  var redraw = function() {
    if(canvas)
      cgame.board.draw(canvas, cgame.notationMap, cgame.showNotations);
    if(icanvas) {
      icanvas.getContext("2d").clearRect(0, 0, icanvas.width, icanvas.height);
      mouseHandler({offsetX: mouseHandler.prev.rawX, offsetY: mouseHandler.prev.rawY}, false);
    }
  }

  document.addEventListener("keydown", e => {
    if(e.isComposing || e.keyCode === 229) {
      return;
    }

    // keyCode == 13 is enter/return
    if(e.keyCode == 13) {
      endTurn();
    }

    // keyCode == 37 is left arrow
    if(e.keyCode == 37) {
      e.preventDefault();
      if(cgame.undo(table))
        redraw();
    }

    // keyCode == 39 is right arrow
    if(e.keyCode == 39) {
      e.preventDefault();
      if(cgame.redo(table))
        redraw();
    }

    // keyCode == 27 is escape
    if(e.keyCode == 27) {
      if(cgame.action == "h" || cgame.action == "v" || cgame.action == "j") {
        cgame.action = "";
        mouseHandler.prev.px = -1;
        mouseHandler.prev.py = -1;
        icanvas.getContext("2d").clearRect(0, 0, icanvas.width, icanvas.height);
        mouseHandler({offsetX: mouseHandler.prev.rawX, offsetY: mouseHandler.prev.rawY}, false);
      }
    }
  });

  $( "#end-turn" ).click(function() {
    endTurn();
  });

  $( "#previous-move" ).click(function() {
    if(cgame.undo(table))
      redraw();
  });

  $( "#next-move" ).click(function() {
    if(cgame.redo(table))
      redraw();
  });

  if(autoResize) {
    resizeCanvases();
  }
  cgame.board.draw(canvas, cgame.notationMap, cgame.showNotations);
}

function terminalStateEval(gameState) {
  // returns null or a number (do not mutate gameState)
  if(gameState.winner) {
    if(gameState.winner == gameState.turn)
      return Infinity;
    else
      return -Infinity;
  } else {
    return null;
  }
}

function listMoves(gameState) {
  // do not mutate gameState
  // populate with at least one (1) valid move
  return gameState.getMoves();
}

function nextState(gameState, moveToMake) {
    // returns a new game state object; does not mutate gameState
//   var cb = new CBoard(H, W);
//   var cg = new CGame(cb);
  var cg = new CGame();
  cg.loadFromGame(gameState);
//   for(let move of gameState.history)
//     cg.doMove(move);
  cg.doMove(moveToMake);
  return cg;
}

function evaluate(gameState) {
//   var blackArea = 0;
//   var whiteArea = 0;
  var blackChains = 0;
  var whiteChains = 0;
  var blackChainArea = 0;
  var whiteChainArea = 0;
//   for(let cp of gameState.board.blMap.values()) {
//     if(cp.color == "b")
//       blackArea += (cp.tr.y - cp.bl.y) * (cp.tr.x - cp.bl.x);
//     else
//       whiteArea += (cp.tr.y - cp.bl.y) * (cp.tr.x - cp.bl.x);
//   }
  for(let chain of gameState.chains) {
    var minX = gameState.board.width;
    var maxX = 0;
    var minY = gameState.board.height;
    var maxY = 0;
    var color = undefined;
    var area = 0;
    for(let cp of chain) {
      area += (cp.tr.y - cp.bl.y) * (cp.tr.x - cp.bl.x);
      if(!color)
        color = cp.color;
      if(cp.bl.x < minX)
        minX = cp.bl.x;
      if(cp.tr.x > maxX)
        maxX = cp.tr.x;
      if(cp.bl.y < minY)
        minY = cp.bl.y;
      if(cp.tr.y > maxY)
        maxY = cp.tr.y;
    }
    if(color == "b") {
      blackChains += (maxY - minY) * (maxY - minY) + (maxX - minX) * (maxX - minX);
      blackChainArea += area * area;
    } else {
      whiteChains += (maxY - minY) * (maxY - minY) + (maxX - minX) * (maxX - minX);
      whiteChainArea += area * area;
    }
  }
//   console.log(gameState.history);
//   console.log(blackChains - whiteChains + blackArea - whiteArea);
//   console.log(blackChains, whiteChains);
//   callback( blackArea - whiteArea );
  const chainWeight = 1;
  const chainAreaWeight = 0.3;
  var value = chainWeight * (blackChains - whiteChains) + chainAreaWeight * (blackChainArea - whiteChainArea);
  if(gameState.turn == "b")
    return value
  else
    return -1 * value;
    // returns a number, does not mutate gameState
}
